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
		 * @return Schema
		 */
		public function generateSchema(array $options = array(), array $customTypes = array())
		{
			$generator = new Generator($options);
			$this->customTypes = $customTypes;
			$entities = $this->findEntities();

			foreach ($entities as $entity) {
				$this->generateEntity($generator, $entity);
			}

			$generator->createHasManyTables();
			$generator->createRelationships();

			return $generator->getSchema();
		}


		protected function generateEntity(Generator $generator, $entityClass)
		{
			$reflection = call_user_func(array($entityClass, 'getReflection'), $this->mapper);
			$properties = $reflection->getEntityProperties();

			if (empty($properties)) {
				return;
			}

			$tableName = $this->mapper->getTable($entityClass);
			$tablePrimaryColumn = $this->mapper->getPrimaryKey($tableName);
			$table = $generator->createTable($tableName, $tablePrimaryColumn);
			$propertySources = array();

			foreach ($this->getFamilyLine($reflection) as $member) {
				$docComment = $member->getDocComment();
				$this->extractTableComment($table, $docComment);
				$this->extractTableOption($table, $docComment);
				$this->extractTableIndexes($generator, $tableName, $member, 'primary');
				$this->extractTableIndexes($generator, $tableName, $member, 'unique');
				$this->extractTableIndexes($generator, $tableName, $member, 'index');

				$memberClass = $member->getName();
				$memberProperties = array_keys($member->getEntityProperties());

				foreach ($memberProperties as $memberProperty) {
					if (!isset($propertySources[$memberProperty])) {
						$propertySources[$memberProperty] = $memberClass;
					}
				}
			}

			// hack - primary column must be always first (@property-read is always last)
			if (isset($properties[$tablePrimaryColumn])) {
				$properties = array($tablePrimaryColumn => $properties[$tablePrimaryColumn]) + $properties;
			}

			foreach ($properties as $property) {
				if ($property->hasRelationship()) {
					$relationship = $property->getRelationship();

					if ($relationship instanceof Relationship\HasMany) {
						$this->addHasManyRelationship($generator, $relationship, $tableName);
						continue; // virtual column

					} elseif ($relationship instanceof Relationship\HasOne) {
						$generator->addRelationship($tableName, $relationship->getColumnReferencingTargetTable(), $relationship->getTargetTable());

					} elseif ($relationship instanceof Relationship\BelongsTo) {
						$generator->addRelationship($relationship->getTargetTable(), $relationship->getColumnReferencingSourceTable(), $tableName);
						continue; // virtual column

					} else {
						throw new \RuntimeException('Unknow relationship ' . (is_object($relationship) ? get_class($relationship) : gettype($relationship))); // TODO
					}
				}

				$propertyName = $property->getName();
				$entitySource = isset($propertySources[$propertyName]) ? $propertySources[$propertyName] : $entityClass;
				$propertySource = $entitySource . '::' . $propertyName;
				$columnName = $property->getColumn();
				$isPrimaryColumn = $generator->isTablePrimaryColumn($tableName, $columnName);
				$columnType = NULL;

				if (!$property->hasRelationship()) {
					$columnType = $this->extractColumnType($property, $isPrimaryColumn, $entityClass);
				}

				$column = $generator->addColumn($tableName, $columnName, $columnType, $entitySource);
				$column->setNullable($property->isNullable());

				$this->extractColumnComment($column, $property);
				$this->extractColumnAutoIncrement($column, $property, $generator->getTablePrimaryColumn($tableName));
				$this->extractColumnIndex($generator, $property, 'primary', $tableName, $columnName, $propertySource);
				$this->extractColumnIndex($generator, $property, 'unique', $tableName, $columnName, $propertySource);
				$this->extractColumnIndex($generator, $property, 'index', $tableName, $columnName, $propertySource);
			}

			if (!$generator->hasPrimaryIndex($tableName)) {
				$generator->addPrimaryIndex($tableName, $generator->getTablePrimaryColumn($tableName));
			}
		}


		/**
		 * @return void
		 */
		protected function extractTableComment(SqlSchema\Table $table, $docComment)
		{
			// @schema-comment comment
			// @schemaComment comment
			$annotations = array(
				'schema-comment',
				'schemaComment',
			);

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $docComment) as $definition) {
					$comment = trim($definition);
					$table->setComment($comment !== '' ? $comment : NULL);
				}
			}
		}


		/**
		 * @return void
		 */
		protected function extractTableOption(SqlSchema\Table $table, $docComment)
		{
			// @schema-option option value
			// @schemaOption option value
			$annotations = array(
				'schema-option',
				'schemaOption',
			);

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $docComment) as $definition) {
					$definition = trim($definition);

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

					$table->setOption(strtoupper($option), $value !== '' ? $value : NULL);
				}
			}
		}


		/**
		 * @return void
		 */
		protected function extractTableIndexes(Generator $generator, $tableName, Reflection\EntityReflection $reflection, $indexType)
		{
			// @schema-<type> property1, property2
			// @schema<Type> property, property2
			$entityClass = $reflection->getName();
			$annotations = array(
				'schema-' . $indexType,
				'schema' . ucfirst($indexType),
			);

			foreach ($annotations as $annotation) {
				foreach (AnnotationsParser::parseAnnotationValues($annotation, $reflection->getDocComment()) as $definition) {
					$properties = array_map('trim', explode(',', $definition));
					$columns = array();

					foreach ($properties as $property) {
						$columns[] = $this->mapper->getColumn($property);
					}

					$this->addIndexByType($generator, $type, $tableName, $columns, $entityClass);
				}
			}
		}


		/**
		 * @return DataType
		 */
		protected function extractColumnType(Reflection\Property $property, $isPrimaryColumn, $entityClass)
		{
			$type = NULL;
			$parameters = NULL;
			$options = array();

			if ($property->hasCustomFlag('schema-type')) {
				$datatype = $this->parseTypeFlag($property, 'schema-type');
				$type = $datatype->getType();
				$parameters = $datatype->getParameters();
				$options = $datatype->getOptions();

			} elseif ($property->hasCustomFlag('schemaType')) {
				$datatype = $this->parseTypeFlag($property, 'schemaType');
				$type = $datatype->getType();
				$parameters = $datatype->getParameters();
				$options = $datatype->getOptions();

			} elseif ($property->isBasicType()) {
				$propertyType = $property->getType();

				if ($propertyType === 'integer') {
					$type = 'INT';

					if ($isPrimaryColumn) {
						$options[] = SqlSchema\Column::OPTION_UNSIGNED;
					}

				} elseif ($propertyType === 'boolean') {
					$type = 'TINYINT';
					$parameters = array(1);
					$options[] = 'UNSIGNED';

				} elseif ($propertyType === 'float') {
					$type = 'DOUBLE';

				} elseif ($propertyType === 'string') {
					$type = 'TEXT';
				}

			} else { // object
				$className = $property->getType();
				$isDateTime = is_subclass_of($className, 'DateTime')
					|| is_subclass_of($className, 'DateTimeInterface');

				if ($isDateTime) {
					$type = 'DATETIME';
				}
			}

			if ($type === NULL) {
				throw new MissingException("Missing type for property '{$property->getName()}' in entity '{$entityClass}'.");
			}

			$lowerType = strtolower($type);

			if (isset($this->customTypes[$lowerType])) {
				$columnType = $this->customTypes[$lowerType];
				$type = $columnType->getType();
				$parameters = $columnType->getParameters();
				$options = array_merge($options, $columnType->getOptions());

			}

			return new DataType($type, $parameters, $options);
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
		 * @return void
		 */
		protected function extractColumnComment(SqlSchema\Column $column, Reflection\Property $property)
		{
			if ($property->hasCustomFlag('schema-comment')) {
				$column->setComment($property->getCustomFlagValue('schema-comment'));

			} elseif ($property->hasCustomFlag('schemaComment')) {
				$column->setComment($property->getCustomFlagValue('schemaComment'));
			}
		}


		/**
		 * @return void
		 */
		protected function extractColumnAutoIncrement(SqlSchema\Column $column, Reflection\Property $property, $primaryKey)
		{
			if ($property->hasCustomFlag('schema-autoIncrement') || $property->hasCustomFlag('schemaAutoIncrement')) {
				$column->setAutoIncrement(TRUE);

			} else { // auto-detect
				$column->setAutoIncrement($primaryKey === $column->getName() && $property->getType() === 'integer');
			}
		}


		/**
		 * @return void
		 */
		protected function extractColumnIndex(Generator $generator, Reflection\Property $property, $type, $tableName, $columnName, $propertySource)
		{
			$flags = array(
				'schema-' . $type,
				'schema' . ucfirst($type),
			);

			foreach ($flags as $flag) {
				if ($property->hasCustomFlag($flag)) {
					$this->addIndexByType($generator, $type, $tableName, $columnName, $propertySource);
					return;
				}
			}
		}


		protected function addIndexByType(Generator $generator, $indexType, $tableName, $columns, $sourceId)
		{
			if ($indexType === 'index') {
				$generator->addIndex($tableName, $columns, $sourceId);

			} elseif ($indexType === 'unique') {
				$generator->addUniqueIndex($tableName, $columns, $sourceId);

			} elseif ($indexType === 'primary') {
				$generator->addPrimaryIndex($tableName, $columns, $sourceId);

			} else {
				throw new InvalidArgumentException("Unknow index type '$indexType'.");
			}
		}


		protected function addHasManyRelationship(Generator $generator, Relationship\HasMany $relationship, $sourceTable)
		{
			$relationshipTable = $relationship->getRelationshipTable();
			$sourceColumn = $relationship->getColumnReferencingSourceTable();
			$targetTable = $relationship->getTargetTable();
			$targetColumn = $relationship->getColumnReferencingTargetTable();

			if ($this->mapper->getRelationshipTable($sourceTable, $targetTable) === $relationshipTable) {
				$generator->addHasManyTable(
					$relationshipTable,
					$sourceTable,
					$sourceColumn,
					$targetTable,
					$targetColumn
				);

			} else {
				$generator->addHasManyTable(
					$relationshipTable,
					$targetTable,
					$targetColumn,
					$sourceTable,
					$sourceColumn
				);
			}
			// $relationshipTable = $generator->createTable();
			// $generator->addColumn($relationship->getRelationshipTable(), $relationship->getColumnReferencingSourceTable(), NULL, NULL);
			// $generator->addColumn($relationship->getRelationshipTable(), $relationship->getColumnReferencingTargetTable(), NULL, NULL);
		}


		/**
		 * Returns list of FQN class names.
		 * @return string[]
		 */
		protected function findEntities()
		{
			$robot = new Nette\Loaders\RobotLoader;
			$robot->addDirectory($this->directories);
			$robot->acceptFiles = '*.php';
			$robot->rebuild();
			$classes = array_keys($robot->getIndexedClasses());
			$entities = array();

			foreach ($classes as $class) {
				$accept = class_exists($class)
					&& ($rc = new \ReflectionClass($class))
					&& $rc->isSubclassOf('LeanMapper\\Entity')
					&& !$rc->isAbstract();

				if ($accept) {
					$entities[] = $class;
				}
			}

			return $entities;
		}


		/**
		 * @return Reflection\EntityReflection[]
		 */
		protected function getFamilyLine(Reflection\EntityReflection $reflection)
		{
			$line = array($member = $reflection);

			while ($member = $member->getParentClass()) {
				if ($member->name === 'LeanMapper\Entity') {
					break;
				}

				$line[] = $member;
			}

			return array_reverse($line);
		}
	}
