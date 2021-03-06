<?php

	namespace Inlm\SchemaGenerator\Extractors;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\DataType;
	use Inlm\SchemaGenerator\IExtractor;
	use Inlm\SchemaGenerator\EmptyException;
	use Inlm\SchemaGenerator\InvalidArgumentException;
	use Inlm\SchemaGenerator\MissingException;
	use Inlm\SchemaGenerator\Utils\Generator;
	use Inlm\SchemaGenerator\Utils\DataTypeParser;
	use Inlm\SchemaGenerator\Utils\DataTypeProcessor;
	use Nette;
	use LeanMapper\IMapper;
	use LeanMapper\Reflection;
	use LeanMapper\Reflection\AnnotationsParser;
	use LeanMapper\Relationship;


	class LeanMapperExtractor implements IExtractor
	{
		/** @var IMapper */
		protected $mapper;

		/** @var string|string[] */
		protected $directories;

		/** @var array  [name => DataType] */
		protected $customTypes;

		/** @var string|NULL */
		protected $databaseType;

		/** @var Generator */
		protected $generator;


		/**
		 * @param  string|string[]
		 * @param  IMapper
		 */
		public function __construct($directories, IMapper $mapper)
		{
			$this->directories = $directories;
			$this->mapper = $mapper;
		}


		/**
		 * @return SqlSchema\Schema
		 */
		public function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL)
		{
			$this->generator = new Generator($options, $databaseType);
			$this->customTypes = $customTypes;
			$this->databaseType = $databaseType;
			$entities = $this->findEntities();

			foreach ($entities as $entity) {
				$this->generateEntity($entity);
			}

			$schema = $this->generator->finalize();
			$this->generator = NULL;
			return $schema;
		}


		protected function generateEntity($entityClass)
		{
			$reflection = call_user_func([$entityClass, 'getReflection'], $this->mapper);
			$properties = $reflection->getEntityProperties();

			if (empty($properties) || $this->isEntityIgnored($reflection->getDocComment())) {
				return;
			}

			$tableName = $this->mapper->getTable($entityClass);
			$tablePrimaryColumn = $this->mapper->getPrimaryKey($tableName);
			$this->generator->createTable($tableName, $tablePrimaryColumn);

			foreach ($this->getFamilyLine($reflection) as $member) {
				$docComment = $member->getDocComment();

				if ($docComment === FALSE) {
					continue;
				}

				$this->extractTableComment($tableName, $docComment);
				$this->extractTableOption($tableName, $docComment);
				$this->extractTableIndexes($tableName, $member, 'primary', $entityClass);
				$this->extractTableIndexes($tableName, $member, 'unique', $entityClass);
				$this->extractTableIndexes($tableName, $member, 'index', $entityClass);
			}

			// hack - primary column must be always first (@property-read is always last)
			if (isset($properties[$tablePrimaryColumn])) {
				$properties = [$tablePrimaryColumn => $properties[$tablePrimaryColumn]] + $properties;
			}

			foreach ($properties as $property) {
				if ($property->hasRelationship()) {
					$relationship = $property->getRelationship();

					if ($relationship instanceof Relationship\HasMany) {
						$this->addHasManyRelationship($relationship, $tableName);
						continue; // virtual column

					} elseif ($relationship instanceof Relationship\HasOne) {
						$this->generator->addRelationship($tableName, $relationship->getColumnReferencingTargetTable(), $relationship->getTargetTable());

					} elseif ($relationship instanceof Relationship\BelongsTo) {
						$this->generator->addRelationship($relationship->getTargetTable(), $relationship->getColumnReferencingSourceTable(), $tableName);
						continue; // virtual column

					} else {
						throw new \RuntimeException('Unknow relationship ' . (is_object($relationship) ? get_class($relationship) : gettype($relationship))); // TODO
					}
				}

				$propertyName = $property->getName();
				$columnName = $property->getColumn();
				$isPrimaryColumn = $columnName === $tablePrimaryColumn;
				$columnType = NULL;

				if (!$property->hasRelationship()) {
					$columnType = $this->extractColumnType($property, $isPrimaryColumn, $entityClass);
				}

				$this->generator->addColumn($tableName, $columnName, $columnType);
				$this->generator->setColumnNullable($tableName, $columnName, $property->isNullable());

				$this->extractColumnComment($tableName, $columnName, $property);
				$this->extractColumnAutoIncrement($tableName, $columnName, $property, $isPrimaryColumn);
				$this->extractColumnIndex($property, 'primary', $tableName, $columnName);
				$this->extractColumnIndex($property, 'unique', $tableName, $columnName);
				$this->extractColumnIndex($property, 'index', $tableName, $columnName);
			}
		}


		/**
		 * @param  string
		 * @return bool
		 */
		protected function isEntityIgnored($docComment)
		{
			// @schema-ignore
			// @schemaIgnore
			$annotations = [
				'schema-ignore',
				'schemaIgnore',
			];

			foreach ($annotations as $annotation) {
				if (AnnotationsParser::parseSimpleAnnotationValue($annotation, $docComment) !== NULL) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @return void
		 */
		protected function extractTableComment($tableName, $docComment)
		{
			// @schema-comment comment
			// @schemaComment comment
			$annotations = [
				'schema-comment',
				'schemaComment',
			];

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $docComment) as $comment) {
					$this->generator->setTableComment($tableName, $comment);
				}
			}
		}


		/**
		 * @return void
		 */
		protected function extractTableOption($tableName, $docComment)
		{
			// @schema-option option value
			// @schemaOption option value
			$annotations = [
				'schema-option',
				'schemaOption',
			];

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $docComment) as $definition) {
					$definition = trim($definition);

					if ($definition === '*/') { // fix for bug in AnnotationsParser::parseAnnotationValues
						$definition = '';
					}

					if ($definition === '') {
						throw new EmptyException("Empty definition of '@{$annotation}'.");
					}

					$option = $definition;
					$value = NULL;

					if (strpos($definition, ' ') !== FALSE) {
						list($option, $value) = explode(' ', $definition, 2);
						$option = trim($option);
						$value = trim($value);
					}

					if ($option === '') {
						throw new MissingException("Missing option name in '@{$annotation}'.");
					}

					$this->generator->setTableOption($tableName, $option, $value);
				}
			}
		}


		/**
		 * @return void
		 */
		protected function extractTableIndexes($tableName, Reflection\EntityReflection $reflection, $indexType, $entityClass)
		{
			// @schema-<type> property1, property2
			// @schema<Type> property, property2
			$annotations = [
				'schema-' . $indexType,
				'schema' . ucfirst($indexType),
			];

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $reflection->getDocComment()) as $definition) {
					$properties = array_map('trim', explode(',', $definition));
					$columns = [];

					foreach ($properties as $propertyName) {
						$property = $reflection->getEntityProperty($propertyName);

						if ($property !== NULL) {
							$columns[] = $property->getColumn();

						} else { // fallback
							$columns[] = $this->mapper->getColumn($entityClass, $propertyName);
						}
					}

					$this->addIndexByType($indexType, $tableName, $columns);
				}
			}
		}


		/**
		 * @return DataType
		 */
		protected function extractColumnType(Reflection\Property $property, $isPrimaryColumn, $entityClass)
		{
			$datatype = NULL;

			if ($property->hasCustomFlag('schema-type')) {
				$datatype = $this->parseTypeFlag($property, 'schema-type');

			} elseif ($property->hasCustomFlag('schemaType')) {
				$datatype = $this->parseTypeFlag($property, 'schemaType');
			}

			try {
				return DataTypeProcessor::process($property->getType(), $datatype, $isPrimaryColumn, $this->customTypes, $this->databaseType);

			} catch (MissingException $e) {
				throw new MissingException("Missing type for property '{$property->getName()}' in entity '{$entityClass}'.", 0, $e);
			}
		}


		/**
		 * @param  Reflection\Property
		 * @param  string
		 * @return DataType
		 * @throws InvalidArgumentException
		 */
		protected function parseTypeFlag(Reflection\Property $property, $flagName)
		{
			$s = $property->getCustomFlagValue($flagName);
			try {
				return DataTypeParser::parse($s, DataTypeParser::SYNTAX_ALTERNATIVE);

			} catch (\Exception $e) { // TODO
				throw new InvalidArgumentException("Malformed m:{$flagName} definition for property '{$property->getName()}' in entity ''.");
			}
		}


		/**
		 * @param  string
		 * @param  string
		 * @return void
		 */
		protected function extractColumnComment($tableName, $columnName, Reflection\Property $property)
		{
			if ($property->hasCustomFlag('schema-comment')) {
				$this->generator->setColumnComment($tableName, $columnName, $property->getCustomFlagValue('schema-comment'));

			} elseif ($property->hasCustomFlag('schemaComment')) {
				$this->generator->setColumnComment($tableName, $columnName, $property->getCustomFlagValue('schemaComment'));
			}
		}


		/**
		 * @param  string
		 * @param  string
		 * @return void
		 */
		protected function extractColumnAutoIncrement($tableName, $columnName, Reflection\Property $property, $isPrimaryColumn)
		{
			if ($property->hasCustomFlag('schema-autoIncrement') || $property->hasCustomFlag('schemaAutoIncrement')) {
				$this->generator->setColumnAutoIncrement($tableName, $columnName, TRUE);

			} else { // auto-detect
				$this->generator->setColumnAutoIncrement($tableName, $columnName, $isPrimaryColumn && $property->getType() === 'integer');
			}
		}


		/**
		 * @return void
		 */
		protected function extractColumnIndex(Reflection\Property $property, $type, $tableName, $columnName)
		{
			$flags = [
				'schema-' . $type,
				'schema' . ucfirst($type),
			];

			foreach ($flags as $flag) {
				if ($property->hasCustomFlag($flag)) {
					$this->addIndexByType($type, $tableName, $columnName);
					return;
				}
			}
		}


		protected function addIndexByType($indexType, $tableName, $columns)
		{
			if ($indexType === 'index') {
				$this->generator->addIndex($tableName, $columns);

			} elseif ($indexType === 'unique') {
				$this->generator->addUniqueIndex($tableName, $columns);

			} elseif ($indexType === 'primary') {
				$this->generator->addPrimaryIndex($tableName, $columns);

			} else {
				throw new InvalidArgumentException("Unknow index type '$indexType'.");
			}
		}


		protected function addHasManyRelationship(Relationship\HasMany $relationship, $sourceTable)
		{
			$relationshipTable = $relationship->getRelationshipTable();
			$sourceColumn = $relationship->getColumnReferencingSourceTable();
			$targetTable = $relationship->getTargetTable();
			$targetColumn = $relationship->getColumnReferencingTargetTable();

			if ($this->mapper->getRelationshipTable($sourceTable, $targetTable) === $relationshipTable) {
				$this->generator->addHasManyTable(
					$relationshipTable,
					$sourceTable,
					$sourceColumn,
					$targetTable,
					$targetColumn
				);

			} else {
				$this->generator->addHasManyTable(
					$relationshipTable,
					$targetTable,
					$targetColumn,
					$sourceTable,
					$sourceColumn
				);
			}
		}


		/**
		 * Returns list of FQN class names.
		 * @return string[]
		 */
		protected function findEntities()
		{
			$directories = is_array($this->directories) ? $this->directories : [$this->directories];
			$phpClassFinder = new \Inlm\SchemaGenerator\Utils\PhpClassFinder($directories);
			$classes = $phpClassFinder->find();
			$entities = [];

			foreach ($classes->getClasses() as $class) {
				$accept = !$class->isAbstract()
					&& $classes->isSubclassOf($class, \LeanMapper\Entity::class);

				$entityClass = $class->getName();

				if ($accept && !class_exists($entityClass)) {
					$class->loadFile();
				}

				if ($accept && class_exists($entityClass, FALSE)) {
					$entities[] = $class->getName();
				}
			}

			sort($entities, SORT_STRING);
			return $entities;
		}


		/**
		 * @return Reflection\EntityReflection[]
		 */
		protected function getFamilyLine(Reflection\EntityReflection $reflection)
		{
			$line = [$member = $reflection];

			while ($member = $member->getParentClass()) {
				if ($member === NULL || $member->name === \LeanMapper\Entity::class) {
					break;
				}

				$line[] = $member;
			}

			return array_reverse($line);
		}
	}
