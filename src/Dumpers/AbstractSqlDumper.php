<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;


	abstract class AbstractSqlDumper implements IDumper
	{
		const MYSQL = 'mysql';

		/** @var SqlGenerator\SqlDocument */
		protected $sqlDocument;

		/** @var string|NULL */
		protected $description;

		/** @var bool */
		protected $started = FALSE;

		/** @var array */
		protected $_tableAlter = array(
			'table' => NULL,
			'statement' => NULL,
		);


		/**
		 * @param  string|NULL
		 * @return void
		 */
		public function start($description = NULL)
		{
			if ($this->started) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('Dumper is already started.');
			}

			$this->sqlDocument = new SqlGenerator\SqlDocument;
			$this->description = $description;
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
					$column->getType(),
					$column->getParameters(),
					$column->getOptions()
				)
					->setNullable($column->isNullable())
					->setAutoIncrement($column->isAutoIncrement())
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
					$foreignKey->getName(),
					$foreignKey->getColumns(),
					$foreignKey->getTargetTable(),
					$foreignKey->getTargetColumns()
				);
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
			$this->getTableAlter($column->getTableName())
				->addColumn(
					$definition->getName(),
					$definition->getType(),
					$definition->getParameters(),
					$definition->getOptions()
				)
					->setNullable($definition->isNullable())
					->setAutoIncrement($definition->isAutoIncrement())
					->setComment($definition->getComment());
		}


		/**
		 * @return void
		 */
		public function updateTableColumn(Diffs\UpdatedTableColumn $column)
		{
			$this->checkIfStarted();
			$definition = $column->getDefinition();
			$this->getTableAlter($column->getTableName())
				->modifyColumn(
					$definition->getName(),
					$definition->getType(),
					$definition->getParameters(),
					$definition->getOptions()
				)
					->setNullable($definition->isNullable())
					->setAutoIncrement($definition->isAutoIncrement())
					->setComment($definition->getComment());
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
			$alter->dropForeignKey($foreignKey->getForeignKeyName());
			$this->addForeignKey($alter, $foreignKey->getDefinition());
		}


		/**
		 * @return void
		 */
		public function removeForeignKey(Diffs\RemovedForeignKey $foreignKey)
		{
			$this->checkIfStarted();
			$this->getTableAlter($foreignKey->getTableName())
				->dropForeignKey($foreignKey->getForeignKeyName());
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
			$this->sqlDocument = NULL;
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
				$definition->getName(),
				$definition->getColumns(),
				$definition->getTargetTable(),
				$definition->getTargetColumns()
			);
			$foreignKey->setOnUpdateAction($definition->getOnUpdateAction());
			$foreignKey->setOnDeleteAction($definition->getOnDeleteAction());
		}


		/**
		 * @param  string
		 * @return SqlGenerator\Statements\AlterTable
		 */
		protected function getTableAlter($tableName)
		{
			if ($this->_tableAlter['table'] !== $tableName) {
				$this->_tableAlter['table'] = $tableName;
				$this->_tableAlter['statement'] = $this->sqlDocument->alterTable($tableName);
			}

			return $this->_tableAlter['statement'];
		}


		/**
		 * @param  string|SqlGenerator\IDriver
		 * @return SqlGenerator\IDriver
		 * @throws \Inlm\SchemaGenerator\InvalidArgumentException
		 */
		protected function prepareDriver($driver)
		{
			if (is_string($driver) && $driver === self::MYSQL) {
				return new SqlGenerator\Drivers\MysqlDriver;

			} elseif (is_object($driver) && $driver instanceof SqlGenerator\IDriver) {
				return $driver;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Driver is not supported.');
		}
	}
