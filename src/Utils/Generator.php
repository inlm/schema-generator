<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\DataType;
	use Inlm\SchemaGenerator\DuplicatedException;
	use Inlm\SchemaGenerator\InvalidArgumentException;
	use Inlm\SchemaGenerator\MissingException;


	class Generator
	{
		/** @var array */
		private $options;

		/** @var SqlSchema\Schema */
		private $schema;

		/** @var array  [name => SqlSchema\Table] */
		private $tables = array();

		/** @var array  [name => SqlSchema\Table] */
		private $columns = array();

		/** @var array  [name => SqlSchema\Table] */
		private $indexes = array();

		/** @var array  [name => SqlSchema\ForeignKey] */
		private $foreignKeys = array();

		/** @var array */
		private $relationships = array();

		/** @var array */
		private $hasManyTables = array();


		public function __construct(array $options = array())
		{
			$this->options = $options;
			$this->schema = new SqlSchema\Schema;
		}


		/**
		 * @return SqlSchema\Schema
		 */
		public function getSchema()
		{
			return $this->schema;
		}


		/**
		 * @return SqlSchema\Schema
		 */
		public function finalize()
		{
			$this->createHasManyTables();
			$this->createRelationships();

			// for Single Table Inheritance - makes some columns nullable
			foreach ($this->columns as $tableName => $columns) {
				if (!isset($this->tables[$tableName])) {
					continue;
				}

				foreach ($columns as $column) {
					$definition = $column['column'];

					if (!$definition->isNullable() && $column['created'] < $this->tables[$tableName]->getNumberOfCreation()) {
						$definition->setNullable();
					}
				}
			}

			return $this->schema;
		}


		/**
		 * @param  string
		 * @param  string
		 * @param  string
		 * @param  string
		 * @return self
		 */
		public function addRelationship($sourceTable, $sourceColumn, $targetTable)
		{
			if (isset($this->relationships[$sourceTable][$sourceColumn])) {
				if ($this->relationships[$sourceTable][$sourceColumn] !== $targetTable) {
					throw new DuplicatedException("Already exists relationship for column '$sourceTable'.'$sourceColumn'.");
				}
			}

			$this->relationships[$sourceTable][$sourceColumn] = $targetTable;
			return $this;
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function addHasManyTable($tableName, $sourceTable, $sourceColumn, $targetTable, $targetColumn)
		{
			$hasManyTable = new GeneratorHasManyTable($sourceTable, $sourceColumn, $tableName, $targetTable, $targetColumn);

			if (isset($this->hasManyTables[$tableName]) && $this->hasManyTables[$tableName]->hasDifferences($hasManyTable)) {
				throw new DuplicatedException("HasManyTable already exists for different relation.");
			}

			$this->hasManyTables[$tableName] = $hasManyTable;
			$this->addRelationship($tableName, $sourceColumn, $sourceTable);
			$this->addRelationship($tableName, $targetColumn, $targetTable);
			return $this;
		}


		/**
		 * @return void
		 */
		public function createHasManyTables()
		{
			foreach ($this->hasManyTables as $hasManyTable => $data) {
				if (!$this->hasTable($hasManyTable)) {
					$table = $this->createTable($hasManyTable);
					$this->addColumn($hasManyTable, $data->getSourceColumn(), NULL, 'VIRTUAL');
					$this->addColumn($hasManyTable, $data->getTargetColumn(), NULL, 'VIRTUAL');
					$this->addPrimaryIndex($hasManyTable, array($data->getSourceColumn(), $data->getTargetColumn()), 'VIRTUAL');
					$this->addIndex($hasManyTable, $data->getTargetColumn(), 'VIRTUAL');
				}
			}
		}


		/**
		 * @return void
		 */
		public function createRelationships()
		{
			foreach ($this->relationships as $sourceTable => $sourceColumns) {
				foreach ($sourceColumns as $sourceColumn => $targetTable) {
					if (!$this->hasTable($sourceTable)) {
						throw new MissingException("Missing source table '$sourceTable' for relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					if (!$this->hasTable($targetTable)) {
						throw new MissingException("Missing target table '$targetTable' for relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					if ($this->tables[$targetTable]->getPrimaryColumn() === NULL) {
						throw new MissingException("Table '$targetTable' has no primary column.");
					}

					$targetColumn = $this->tables[$targetTable]->getPrimaryColumn();

					$_sourceTable = $this->tables[$sourceTable]->getDefinition();
					$_sourceColumn = $_sourceTable->getColumn($sourceColumn);

					$_targetTable = $this->tables[$targetTable]->getDefinition();
					$_targetColumn = $_targetTable->getColumn($targetColumn);

					if ($_sourceColumn === NULL) {
						throw new MissingException("Missing column '$sourceTable'.'$sourceColumn'.");
					}

					if ($_targetColumn === NULL) {
						throw new MissingException("Missing column '$targetTable'.'$targetColumn'.");
					}

					$_sourceType = $_sourceColumn->getType();
					$_targetType = $_targetColumn->getType();

					if ($_sourceType !== NULL) {
						throw new DuplicatedException("Column '$sourceTable'.'$sourceColumn' has already data type. Column is required in relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					if ($_targetType === NULL) {
						throw new MissingException("Column '$targetTable'.'$targetColumn' has no data type. Column is required in relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					$_sourceColumn->setType($_targetColumn->getType());
					$_sourceColumn->setParameters($_targetColumn->getParameters());
					$_sourceColumn->setOptions($_targetColumn->getOptions());

					$_sourceTable->addForeignKey(
						$this->formatForeignKey($sourceTable, $sourceColumn),
						$sourceColumn,
						$targetTable,
						$targetColumn
					);
				}
			}
		}


		/**
		 * @param  string
		 * @param  string|NULL
		 * @return SqlSchema\Table
		 */
		public function createTable($tableName, $primaryColumn = NULL)
		{
			if (!isset($this->tables[$tableName])) {
				$table = $this->schema->addTable($tableName);

				foreach ($this->options as $option => $optionValue) {
					$table->setOption($option, $optionValue);
				}

				$this->tables[$tableName] = new GeneratorTable($table, $primaryColumn);
			}

			$this->tables[$tableName]->markAsCreated();
			return $this->tables[$tableName]->getDefinition();
		}


		/**
		 * @param  string
		 * @return SqlSchema\Table
		 */
		public function getTable($tableName)
		{
			if (!$this->hasTable($tableName)) {
				throw new MissingException("Missing table '$tableName'.");
			}

			return $this->tables[$tableName]->getDefinition();
		}


		/**
		 * @param  string
		 * @return bool
		 */
		public function hasTable($tableName)
		{
			return isset($this->tables[$tableName]);
		}


		/**
		 * @param  string
		 * @param  string|NULL
		 * @return static
		 */
		public function setTableComment($tableName, $comment)
		{
			$comment = trim($comment);
			$this->getTable($tableName)->setComment($comment !== '' ? $comment : NULL);
		}


		/**
		 * @param  string
		 * @param  string
		 * @param  string|NULL
		 * @return static
		 */
		public function setTableOption($tableName, $option, $value)
		{
			$this->getTable($tableName)->setOption(strtoupper($option), $value !== '' ? $value : NULL);
		}


		/**
		 * @param  string
		 * @return string|NULL
		 */
		public function getTablePrimaryColumn($tableName)
		{
			if (!$this->hasTable($tableName)) {
				throw new MissingException("Missing table '$tableName'.");
			}

			return $this->tables[$tableName]->getPrimaryColumn();
		}


		/**
		 * @param  string
		 * @param  string
		 * @return bool
		 */
		public function isTablePrimaryColumn($tableName, $columnName)
		{
			if (!$this->hasTable($tableName)) {
				throw new MissingException("Missing table '$tableName'.");
			}

			return $columnName !== NULL && $this->tables[$tableName]->getPrimaryColumn() === $columnName;
		}


		/**
		 * @param  string
		 * @param  string
		 * @param  DataType
		 * @param  string|NULL
		 * @return SqlSchema\Column
		 */
		public function addColumn($tableName, $columnName, DataType $columnType = NULL, $sourceId = NULL)
		{
			if (isset($this->columns[$tableName][$columnName])) {
				$column = $this->columns[$tableName][$columnName]['column'];

				if ($columnType) {
					$oldType = $column->getType();
					$oldParameters = $column->getParameters();
					$oldOptions = $column->getOptions();

					if ($oldType === NULL && empty($oldParameters) && empty($oldOptions)) { // type is not filled
						$column->setType($columnType->getType());
						$column->setParameters($columnType->getParameters());
						$column->setOptions($columnType->getOptions());

					} elseif (!$columnType->isCompatible($column->getType(), $column->getParameters(), $column->getOptions())) {
						throw new InvalidArgumentException("Type is not compatible with column $tableName.$columnName");
					}
				}

				$this->columns[$tableName][$columnName]['created']++;
				return $column;
			}

			$table = $this->getTable($tableName);
			$column = $table->addColumn($columnName, NULL, array(), array());

			if ($columnType) {
				$column->setType($columnType->getType());
				$column->setParameters($columnType->getParameters());
				$column->setOptions($columnType->getOptions());
			}

			$this->columns[$tableName][$columnName] = array(
				'source' => $sourceId,
				'column' => $column,
				'created' => 1,
			);
			return $column;
		}


		/**
		 * @param  string
		 * @param  string
		 * @return DataType|NULL
		 */
		public function getColumnType($tableName, $columnName)
		{
			if (!isset($this->columns[$tableName][$columnName])) {
				return NULL;
			}

			$column = $this->columns[$tableName][$columnName];
			return new DataType(
				$column->getType(),
				$column->getParameters(),
				$column->getOptions()
			);
		}


		/**
		 * @param  string
		 * @param  string|string[]
		 * @param  string|NULL
		 * @return self
		 */
		public function addIndex($tableName, $columns, $sourceId = NULL)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_INDEX, $columns, $sourceId);
			return $this;
		}


		/**
		 * @param  string
		 * @param  string|string[]
		 * @param  string|NULL
		 * @return self
		 */
		public function addUniqueIndex($tableName, $columns, $sourceId = NULL)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_UNIQUE, $columns, $sourceId);
			return $this;
		}


		/**
		 * @param  string
		 * @param  string|string[]
		 * @param  string|NULL
		 * @return self
		 */
		public function addPrimaryIndex($tableName, $columns, $sourceId = NULL)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_PRIMARY, $columns, $sourceId);
			return $this;
		}


		/**
		 * @param  string
		 * @return bool
		 */
		public function hasPrimaryIndex($tableName)
		{
			return isset($this->indexes[$tableName][NULL]);
		}


		/**
		 * @param  string
		 * @param  string
		 * @return void
		 */
		protected function addTableIndex($tableName, $type, $columns, $sourceId = NULL)
		{
			$indexName = $type !== SqlSchema\Index::TYPE_PRIMARY ? $this->formatIndexName($columns) : NULL;

			if (isset($this->indexes[$tableName][$indexName])) {
				$origSource = $this->indexes[$tableName][$indexName]['source'];
				$origType = $this->indexes[$tableName][$indexName]['index']->getType();

				if ($origType !== $type) {
					throw new DuplicatedException("Type mismatch for index '$indexName' in table '$tableName'. Original type '$origType', new type '$type'.");
				}

				if ($origSource !== $sourceId) {
					throw new DuplicatedException("Index '$indexName' for table '$tableName' already exists.");
				}

				return;
			}

			$table = $this->getTable($tableName);
			$this->indexes[$tableName][$indexName] = array(
				'source' => $sourceId,
				'index' => $table->addIndex($indexName, $type, $columns),
			);
		}


		/**
		 * @param  string|string[]
		 * @return string
		 */
		protected function formatIndexName($columns)
		{
			if (!is_array($columns)) {
				$columns = array($columns);
			}
			return implode('_', $columns);
		}


		/**
		 * @param  string|string[]
		 * @return string
		 */
		protected function formatForeignKey($table, $columns)
		{
			if (!is_array($columns)) {
				$columns = array($columns);
			}
			return $table . '_fk_' . implode('_', $columns);
		}
	}
