<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Database;
	use Inlm\SchemaGenerator\DataType;
	use Inlm\SchemaGenerator\DuplicatedException;
	use Inlm\SchemaGenerator\InvalidArgumentException;
	use Inlm\SchemaGenerator\MissingException;
	use Inlm\SchemaGenerator\SchemaGenerator;


	class Generator
	{
		/** @var array<string, string> */
		private $options;

		/** @var string|NULL */
		private $databaseType;

		/** @var SqlSchema\Schema */
		private $schema;

		/** @var array<string, GeneratorTable> */
		private $tables = [];

		/** @var array<string, array<string, GeneratorColumn>> */
		private $columns = [];

		/** @var array<string, array<string, GeneratorIndex>> */
		private $indexes = [];

		/** @var array<string, array<string, string>> */
		private $relationships = [];

		/** @var array<string, GeneratorHasManyTable> */
		private $hasManyTables = [];


		/**
		 * @param array<string, string> $options
		 * @param string|NULL $databaseType
		 */
		public function __construct(array $options = [], $databaseType = NULL)
		{
			$this->options = $options;
			$this->databaseType = $databaseType;
			$this->schema = new SqlSchema\Schema;
		}


		/**
		 * @return SqlSchema\Schema
		 */
		public function finalize()
		{
			$this->modifyEmptyParameters();

			// tries to create primary indexes
			foreach ($this->tables as $tableName => $table) {
				$primaryColumn = $table->getPrimaryColumn();

				if ($primaryColumn !== NULL && !$this->hasPrimaryIndex($tableName)) {
					$this->addPrimaryIndex($tableName, $primaryColumn);
				}
			}

			$this->createHasManyTables();
			$this->createRelationships();

			// for Single Table Inheritance - makes some columns nullable
			foreach ($this->columns as $tableName => $columns) {
				if (!isset($this->tables[$tableName])) {
					continue;
				}

				foreach ($columns as $column) {
					$definition = $column->getDefinition();

					if (!$definition->isNullable() && $column->getNumberOfCreation() < $this->tables[$tableName]->getNumberOfCreation()) {
						$definition->setNullable();
					}
				}
			}

			return $this->schema;
		}


		/**
		 * @param  string $sourceTable
		 * @param  string $sourceColumn
		 * @param  string $targetTable
		 * @return static
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
		 * @param  string $tableName
		 * @param  string $sourceTable
		 * @param  string $sourceColumn
		 * @param  string $targetTable
		 * @param  string $targetColumn
		 * @return static
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
		protected function createHasManyTables()
		{
			foreach ($this->hasManyTables as $hasManyTable => $data) {
				if (!$this->hasTable($hasManyTable)) {
					$this->createTable($hasManyTable);
					$this->addColumn($hasManyTable, $data->getSourceColumn(), NULL);
					$this->addColumn($hasManyTable, $data->getTargetColumn(), NULL);
					$this->addPrimaryIndex($hasManyTable, [$data->getSourceColumn(), $data->getTargetColumn()]);
					$this->addIndex($hasManyTable, $data->getTargetColumn());
				}
			}
		}


		/**
		 * @return void
		 */
		protected function createRelationships()
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

					if ($_targetType === NULL) {
						throw new MissingException("Column '$targetTable'.'$targetColumn' has no data type. Column is required in relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					if ($_sourceType !== NULL && !$this->isPrimaryColumnCompatible($_sourceColumn, $_targetColumn)) {
						throw new DuplicatedException("Column '$sourceTable'.'$sourceColumn' has already data type. Column is required in relationship '$sourceTable'.'$sourceColumn' => '$targetTable'.");
					}

					$_sourceColumn->setType($_targetColumn->getType());
					$_sourceColumn->setParameters($_targetColumn->getParameters());
					$_sourceColumn->setOptions($_targetColumn->getOptions());
					$_sourceColumn->setAutoIncrement(FALSE);

					$foreignKeyName = $this->formatForeignKey($sourceTable, $sourceColumn);
					$_sourceTable->addForeignKey(
						$foreignKeyName,
						$sourceColumn,
						$targetTable,
						$targetColumn
					);

					if ($this->databaseType === Database::MYSQL) { // foreign keys requires index
						if (!$this->hasIndexWithFirstColumn($sourceTable, $sourceColumn)) {
							$this->addTableIndex($sourceTable, SqlSchema\Index::TYPE_INDEX, $sourceColumn, $foreignKeyName);
						}
					}
				}
			}
		}


		/**
		 * @param  string $tableName
		 * @param  string|NULL $primaryColumn
		 * @return static
		 */
		public function createTable($tableName, $primaryColumn = NULL)
		{
			if (!isset($this->tables[$tableName])) {
				$table = $this->schema->addTable($tableName);

				foreach ($this->options as $option => $optionValue) {
					$table->setOption($option, $optionValue);
				}

				$this->tables[$tableName] = new GeneratorTable($table, $primaryColumn);

			} else {
				if ($this->tables[$tableName]->getPrimaryColumn() !== $primaryColumn) {
					throw new \RuntimeException("Table '$tableName' already exists with another primary column '{$this->tables[$tableName]->getPrimaryColumn()}'.");
				}
			}

			$this->tables[$tableName]->markAsCreated();
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @return SqlSchema\Table
		 */
		protected function getTableDefinition($tableName)
		{
			if (!$this->hasTable($tableName)) {
				throw new MissingException("Missing table '$tableName'.");
			}

			return $this->tables[$tableName]->getDefinition();
		}


		/**
		 * @param  string $tableName
		 * @return bool
		 */
		protected function hasTable($tableName)
		{
			return isset($this->tables[$tableName]);
		}


		/**
		 * @param  string $tableName
		 * @param  string|NULL $comment
		 * @return static
		 */
		public function setTableComment($tableName, $comment)
		{
			$comment = trim($comment);
			$this->getTableDefinition($tableName)->setComment($comment !== '' ? $comment : NULL);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $option
		 * @param  string|NULL $value
		 * @return static
		 */
		public function setTableOption($tableName, $option, $value)
		{
			$this->getTableDefinition($tableName)->setOption(strtoupper($option), $value !== '' ? $value : NULL);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @param  DataType $columnType
		 * @param  scalar|NULL $defaultValue
		 * @return static
		 */
		public function addColumn($tableName, $columnName, DataType $columnType = NULL, $defaultValue = NULL)
		{
			if (isset($this->columns[$tableName][$columnName])) {
				$column = $this->columns[$tableName][$columnName]->getDefinition();

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

				if ($defaultValue !== NULL) {
					$oldDefaultValue = $column->getDefaultValue();

					if ($oldDefaultValue === NULL) {
						$column->setDefaultValue($defaultValue);

					} elseif ($oldDefaultValue !== $defaultValue) {
						throw new InvalidArgumentException("Column $tableName.$columnName has already default value ($oldDefaultValue !== $defaultValue).");
					}
				}

				$this->columns[$tableName][$columnName]->markAsCreated();
				return $this;
			}

			$table = $this->getTableDefinition($tableName);
			$column = $table->addColumn($columnName, NULL, [], []);

			if ($columnType) {
				$column->setType($columnType->getType());
				$column->setParameters($columnType->getParameters());
				$column->setOptions($columnType->getOptions());
			}

			if ($defaultValue !== NULL) {
				$column->setDefaultValue($defaultValue);
			}

			$this->columns[$tableName][$columnName] = new GeneratorColumn($column);
			$this->columns[$tableName][$columnName]->markAsCreated();
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @param  bool $isNullable
		 * @return static
		 */
		public function setColumnNullable($tableName, $columnName, $isNullable = TRUE)
		{
			$this->getColumnDefinition($tableName, $columnName)->setNullable($isNullable);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @param  string|NULL $comment
		 * @return static
		 */
		public function setColumnComment($tableName, $columnName, $comment)
		{
			$this->getColumnDefinition($tableName, $columnName)->setComment($comment);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @param  bool $isAutoIncrement
		 * @return static
		 */
		public function setColumnAutoIncrement($tableName, $columnName, $isAutoIncrement = TRUE)
		{
			$this->getColumnDefinition($tableName, $columnName)->setAutoIncrement($isAutoIncrement);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @return SqlSchema\Column
		 */
		protected function getColumnDefinition($tableName, $columnName)
		{
			if (!isset($this->columns[$tableName][$columnName])) {
				throw new MissingException("Missing column '$tableName.$columnName'.");
			}

			return $this->columns[$tableName][$columnName]->getDefinition();
		}


		/**
		 * @param  string $tableName
		 * @param  string|string[] $columns
		 * @return static
		 */
		public function addIndex($tableName, $columns)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_INDEX, $columns);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string|string[] $columns
		 * @return static
		 */
		public function addUniqueIndex($tableName, $columns)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_UNIQUE, $columns);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @param  string|string[] $columns
		 * @return static
		 */
		public function addPrimaryIndex($tableName, $columns)
		{
			$this->addTableIndex($tableName, SqlSchema\Index::TYPE_PRIMARY, $columns);
			return $this;
		}


		/**
		 * @param  string $tableName
		 * @return bool
		 */
		protected function hasPrimaryIndex($tableName)
		{
			return isset($this->indexes[$tableName][NULL]);
		}


		/**
		 * @param  string $tableName
		 * @param  string $columnName
		 * @return bool
		 */
		protected function hasIndexWithFirstColumn($tableName, $columnName)
		{
			if (!isset($this->indexes[$tableName])) {
				return FALSE;
			}

			foreach ($this->indexes[$tableName] as $indexName => $generatorIndex) {
				$definition = $generatorIndex->getDefinition();

				foreach ($definition->getColumns() as $indexColumn) {
					if ($indexColumn->getName() === $columnName) {
						return TRUE;
					}

					break;
				}
			}

			return FALSE;
		}


		/**
		 * @param  string $tableName
		 * @param  string $type
		 * @param  string|string[] $columns
		 * @param  string|NULL $indexName
		 * @return void
		 */
		protected function addTableIndex($tableName, $type, $columns, $indexName = NULL)
		{
			if ($indexName === NULL) {
				$indexName = $type !== SqlSchema\Index::TYPE_PRIMARY ? $this->formatIndexName($columns) : NULL;
			}

			if (isset($this->indexes[$tableName][$indexName])) {
				$this->indexes[$tableName][$indexName]->checkCompatibility($type, $columns);
				return;
			}

			$table = $this->getTableDefinition($tableName);
			$this->indexes[$tableName][$indexName] = new GeneratorIndex($tableName, $table->addIndex($indexName, $columns, $type));
		}


		/**
		 * @return bool
		 */
		protected function isPrimaryColumnCompatible(SqlSchema\Column $sourceColumn, SqlSchema\Column $targetColumn)
		{
			if ($sourceColumn->getType() !== $targetColumn->getType()) {
				return FALSE;
			}

			if ($sourceColumn->getParameters() !== $targetColumn->getParameters()) {
				return FALSE;
			}

			if ($sourceColumn->getOptions() !== $targetColumn->getOptions()) {
				return FALSE;
			}

			if ($sourceColumn->isNullable() !== $targetColumn->isNullable()) {
				return FALSE;
			}

			return TRUE;
		}


		/**
		 * @return void
		 */
		protected function modifyEmptyParameters()
		{
			foreach ($this->columns as $tableName => $columns) {
				if (!isset($this->tables[$tableName])) {
					continue;
				}

				foreach ($columns as $column) {
					$definition = $column->getDefinition();
					$parameters = $definition->getParameters();

					if (!empty($parameters)) {
						continue;
					}

					$type = strtolower($definition->getType());
					$options = $definition->getOptions();
					$isUnsigned = array_key_exists(SqlSchema\Column::OPTION_UNSIGNED, $options);

					if ($this->databaseType === Database::MYSQL) {
						if ($type === 'tinyint') {
							$definition->setParameters($isUnsigned ? 3 : 4);

						} elseif ($type === 'smallint') {
							$definition->setParameters($isUnsigned ? 5 : 6);

						} elseif ($type === 'mediumint') {
							$definition->setParameters($isUnsigned ? 8 : 9);

						} elseif ($type === 'int') {
							$definition->setParameters($isUnsigned ? 10 : 11);

						} elseif ($type === 'bigint') {
							$definition->setParameters(20);

						} elseif ($type === 'decimal') {
							$definition->setParameters([10, 0]);
						}
					}
				}
			}
		}


		/**
		 * @param  string|string[] $columns
		 * @return string
		 */
		protected function formatIndexName($columns)
		{
			if (!is_array($columns)) {
				$columns = [$columns];
			}
			return implode('_', $columns);
		}


		/**
		 * @param  string $table
		 * @param  string|string[] $columns
		 * @return string
		 */
		protected function formatForeignKey($table, $columns)
		{
			if (!is_array($columns)) {
				$columns = [$columns];
			}

			if ($pos = strrpos($table, '.')) { // FALSE or 0
				$table = substr($table, $pos + 1);
			}

			return $table . '_fk_' . implode('_', $columns);
		}
	}
