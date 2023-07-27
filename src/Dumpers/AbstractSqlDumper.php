<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Database;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;
	use Inlm\SchemaGenerator\SchemaGenerator;


	abstract class AbstractSqlDumper implements IDumper
	{
		/** @var SqlGenerator\SqlDocument */
		protected $sqlDocument;

		/** @var string|NULL */
		protected $description;

		/** @var string */
		protected $databaseType;

		/** @var string[]|NULL */
		protected $header;

		/** @var bool */
		protected $positionChanges = FALSE;

		/** @var bool */
		protected $started = FALSE;

		/** @var array{table: string|NULL, statement: SqlGenerator\Statements\AlterTable|NULL} */
		protected $_tableAlter = [
			'table' => NULL,
			'statement' => NULL,
		];


		/**
		 * @param  string[]|NULL $header
		 * @return static
		 */
		public function setHeader(array $header = NULL)
		{
			$this->header = $header;
			return $this;
		}


		/**
		 * @param  bool $positionChanges
		 * @return static
		 */
		public function enablePositionChanges($positionChanges = TRUE)
		{
			$this->positionChanges = $positionChanges;
			return $this;
		}


		/**
		 * @param  string $databaseType  see Database::*
		 * @param  string|NULL $description
		 * @return void
		 */
		public function start($databaseType, $description = NULL)
		{
			if ($this->started) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('Dumper is already started.');
			}

			$this->sqlDocument = new SqlGenerator\SqlDocument;
			$this->description = $description;
			$this->databaseType = $databaseType;
			$this->started = TRUE;
		}


		/**
		 * @return void
		 */
		public function createTable(Diffs\CreatedTable $table)
		{
			$this->checkIfStarted();
			$definition = $table->getDefinition();
			$createTable = $this->sqlDocument->createTable($definition->getName());

			$createTable->setComment($definition->getComment());

			foreach ($definition->getOptions() as $option => $value) {
				$createTable->setOption($option, $value);
			}

			foreach ($definition->getColumns() as $column) {
				$createTable->addColumn(
					$column->getName(),
					$this->checkString($column->getType(), 'Missing column type.'),
					$column->getParameters(),
					$this->convertOptions($column->getOptions())
				)
					->setNullable($column->isNullable())
					->setAutoIncrement($column->isAutoIncrement())
					->setDefaultValue($column->getDefaultValue())
					->setComment($column->getComment());
			}

			foreach ($definition->getIndexes() as $index) {
				$tableIndex = $createTable->addIndex($index->getName(), $index->getType());

				foreach ($index->getColumns() as $indexColumn) {
					$tableIndex->addColumn(
						$indexColumn->getName(),
						$indexColumn->getOrder(),
						$indexColumn->getLength()
					);
				}
			}

			foreach ($definition->getForeignKeys() as $foreignKey) {
				$createTable->addForeignKey(
					$this->checkString($foreignKey->getName(), 'Missing foreign key name.'),
					$foreignKey->getColumns(),
					$this->checkString($foreignKey->getTargetTable(), 'Missing foreign key target table.'),
					$foreignKey->getTargetColumns()
				)
					->setOnUpdateAction($foreignKey->getOnUpdateAction())
					->setOnDeleteAction($foreignKey->getOnDeleteAction());
			}
		}


		/**
		 * @return void
		 */
		public function removeTable(Diffs\RemovedTable $table)
		{
			$this->checkIfStarted();
			$this->sqlDocument->dropTable($table->getTableName());
		}


		/**
		 * @return void
		 */
		public function createTableColumn(Diffs\CreatedTableColumn $column)
		{
			$this->checkIfStarted();
			$definition = $column->getDefinition();
			$createdColumn = $this->getTableAlter($column->getTableName())
				->addColumn(
					$definition->getName(),
					$this->checkString($definition->getType(), 'Missing column type.'),
					$definition->getParameters(),
					$this->convertOptions($definition->getOptions())
				)
					->setNullable($definition->isNullable())
					->setAutoIncrement($definition->isAutoIncrement())
					->setDefaultValue($definition->getDefaultValue())
					->setComment($definition->getComment());

			if ($this->positionChanges) {
				if ($column->getAfterColumn() === NULL) {
					$createdColumn->moveToFirstPosition();

				} else {
					$createdColumn->moveAfterColumn($column->getAfterColumn());
				}
			}
		}


		/**
		 * @return void
		 */
		public function updateTableColumn(Diffs\UpdatedTableColumn $column)
		{
			$this->checkIfStarted();

			if (!$this->positionChanges && $column->hasOnlyPositionChange()) {
				return;
			}

			$definition = $column->getDefinition();
			$updatedColumn = $this->getTableAlter($column->getTableName())
				->modifyColumn(
					$definition->getName(),
					$this->checkString($definition->getType(), 'Missing column type.'),
					$definition->getParameters(),
					$this->convertOptions($definition->getOptions())
				)
					->setNullable($definition->isNullable())
					->setAutoIncrement($definition->isAutoIncrement())
					->setDefaultValue($definition->getDefaultValue())
					->setComment($definition->getComment());

			if ($this->positionChanges) {
				if ($column->getAfterColumn() === NULL) {
					$updatedColumn->moveToFirstPosition();

				} else {
					$updatedColumn->moveAfterColumn($column->getAfterColumn());
				}
			}
		}


		/**
		 * @return void
		 */
		public function removeTableColumn(Diffs\RemovedTableColumn $column)
		{
			$this->checkIfStarted();
			$this->getTableAlter($column->getTableName())
				->dropColumn($column->getColumnName());
		}


		/**
		 * @return void
		 */
		public function createTableIndex(Diffs\CreatedTableIndex $index)
		{
			$this->checkIfStarted();
			$alter = $this->getTableAlter($index->getTableName());
			$this->addIndex($alter, $index->getDefinition());
		}


		/**
		 * @return void
		 */
		public function updateTableIndex(Diffs\UpdatedTableIndex $index)
		{
			$this->checkIfStarted();
			$alter = $this->getTableAlter($index->getTableName());
			$alter->dropIndex($index->getIndexName());
			$this->addIndex($alter, $index->getDefinition());
		}


		/**
		 * @return void
		 */
		public function removeTableIndex(Diffs\RemovedTableIndex $index)
		{
			$this->checkIfStarted();
			$this->getTableAlter($index->getTableName())
				->dropIndex($index->getIndexName());
		}


		/**
		 * @return void
		 */
		public function createForeignKey(Diffs\CreatedForeignKey $foreignKey)
		{
			$this->checkIfStarted();
			$alter = $this->getTableAlter($foreignKey->getTableName());
			$this->addForeignKey($alter, $foreignKey->getDefinition());
		}


		/**
		 * @return void
		 */
		public function updateForeignKey(Diffs\UpdatedForeignKey $foreignKey)
		{
			$this->checkIfStarted();
			$alter = $this->getTableAlter($foreignKey->getTableName());
			$alter->dropForeignKey($this->checkString($foreignKey->getForeignKeyName(), 'Missing foreign key name.'));
			$this->addForeignKey($alter, $foreignKey->getDefinition());
		}


		/**
		 * @return void
		 */
		public function removeForeignKey(Diffs\RemovedForeignKey $foreignKey)
		{
			$this->checkIfStarted();
			$this->getTableAlter($foreignKey->getTableName())
				->dropForeignKey($this->checkString($foreignKey->getForeignKeyName(), 'Missing foreign key name.'));
		}


		/**
		 * @return void
		 */
		public function addTableOption(Diffs\AddedTableOption $option)
		{
			$this->checkIfStarted();
			$this->getTableAlter($option->getTableName())
				->setOption($option->getOption(), $option->getValue());
		}


		/**
		 * @return void
		 */
		public function updateTableOption(Diffs\UpdatedTableOption $option)
		{
			$this->checkIfStarted();
			$this->getTableAlter($option->getTableName())
				->setOption($option->getOption(), $option->getValue());
		}


		/**
		 * @return void
		 */
		public function removeTableOption(Diffs\RemovedTableOption $option)
		{
			throw new \Inlm\SchemaGenerator\UnsupportedException('Removing of table options is not supported.');
		}


		/**
		 * @return void
		 */
		public function updateTableComment(Diffs\UpdatedTableComment $comment)
		{
			$this->checkIfStarted();
			$this->getTableAlter($comment->getTableName())
				->setComment($comment->getComment());
		}


		/**
		 * @return void
		 */
		protected function checkIfStarted()
		{
			if (!$this->started) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('Dumper is not started, call $dumper->start().');
			}
		}


		/**
		 * @return void
		 */
		protected function stop()
		{
			$this->started = FALSE;
			$this->description = NULL;
		}


		/**
		 * @return void
		 */
		protected function addIndex(SqlGenerator\Statements\AlterTable $alter, SqlSchema\Index $definition)
		{
			$index = $alter->addIndex($definition->getName(), $definition->getType());

			foreach ($definition->getColumns() as $column) {
				$index->addColumn($column->getName(), $column->getOrder(), $column->getLength());
			}
		}


		/**
		 * @return void
		 */
		protected function addForeignKey(SqlGenerator\Statements\AlterTable $alter, SqlSchema\ForeignKey $definition)
		{
			$foreignKey = $alter->addForeignKey(
				$this->checkString($definition->getName(), 'Missing foreign key name.'),
				$definition->getColumns(),
				$this->checkString($definition->getTargetTable(), 'Missing foreign key target table.'),
				$definition->getTargetColumns()
			);
			$foreignKey->setOnUpdateAction($definition->getOnUpdateAction());
			$foreignKey->setOnDeleteAction($definition->getOnDeleteAction());
		}


		/**
		 * @param  string $tableName
		 * @return SqlGenerator\Statements\AlterTable
		 */
		protected function getTableAlter($tableName)
		{
			if ($this->_tableAlter['table'] !== $tableName) {
				$this->_tableAlter['table'] = $tableName;
				$this->_tableAlter['statement'] = $this->sqlDocument->alterTable($tableName);
			}

			if ($this->_tableAlter['statement'] === NULL) {
				throw new \Inlm\SchemaGenerator\InvalidStateException("Missing alter table statement.");
			}

			return $this->_tableAlter['statement'];
		}


		/**
		 * @return string[]
		 */
		protected function getHeader()
		{
			if ($this->header !== NULL) {
				return $this->header;
			}

			if ($this->databaseType === Database::MYSQL) {
				return [
					'SET foreign_key_checks = 1;',
					'SET time_zone = "SYSTEM";',
					'SET sql_mode = "TRADITIONAL";',
				];
			}

			return [];
		}


		/**
		 * @return string
		 */
		protected function getHeaderBlock()
		{
			$header = implode("\n", $this->getHeader());
			return $header !== '' ? ($header . "\n\n") : '';
		}


		/**
		 * @param  string|object $driver
		 * @return SqlGenerator\IDriver
		 * @throws \Inlm\SchemaGenerator\InvalidArgumentException
		 */
		protected function prepareDriver($driver)
		{
			if ($driver === Database::MYSQL) {
				return new SqlGenerator\Drivers\MysqlDriver;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Driver is not supported.');
		}


		/**
		 * @param  array<string, scalar|NULL> $options
		 * @return array<string, string|NULL>
		 */
		private function convertOptions(array $options)
		{
			$res = [];

			foreach ($options as $optionName => $optionValue) {
				$res[$optionName] = $optionValue !== NULL ? ((string) $optionValue) : NULL;
			}

			return $res;
		}


		/**
		 * @param  string|NULL $value
		 * @param  string $message
		 * @return string
		 */
		private function checkString($value, $message)
		{
			if ($value === NULL) {
				throw new \Inlm\SchemaGenerator\InvalidStateException($message);
			}

			return $value;
		}
	}
