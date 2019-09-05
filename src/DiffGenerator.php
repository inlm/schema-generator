<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema;


	class DiffGenerator
	{
		/** @var SqlSchema\Schema */
		private $old;

		/** @var SqlSchema\Schema */
		private $new;


		public function __construct(SqlSchema\Schema $old, SqlSchema\Schema $new)
		{
			$this->old = $old;
			$this->new = $new;
		}


		/**
		 * @return array  of Diffs\CreatedTable|Diffs\UpdatedTable
		 */
		public function getCreatedAndUpdatedTables()
		{
			$createdTables = $this->getCreatedTables();
			$updatedTables = $this->getUpdatedTables();

			return $this->sortTables(
				$this->new->getTables(),
				array_merge($createdTables, $updatedTables)
			);
		}


		/**
		 * @return Diffs\CreatedTable[]
		 */
		public function getCreatedTables()
		{
			$tables = array();
			$oldTables = array();

			foreach ($this->old->getTables() as $oldTable) {
				$oldTables[$oldTable->getName()] = TRUE;
			}

			foreach ($this->new->getTables() as $newTable) {
				$table = $newTable->getName();

				if (!isset($oldTables[$table])) {
					$tables[] = new Diffs\CreatedTable($newTable);
				}
			}

			return $tables;
		}


		/**
		 * @return Diffs\RemovedTable[]
		 */
		public function getRemovedTables()
		{
			$tables = array();
			$newTables = array();

			foreach ($this->new->getTables() as $newTable) {
				$newTables[$newTable->getName()] = TRUE;
			}

			foreach ($this->old->getTables() as $oldTable) {
				$table = $oldTable->getName();

				if (!isset($newTables[$table])) {
					$tables[] = new Diffs\RemovedTable($table);
				}
			}

			$tables = $this->sortTables($this->old->getTables(), $tables);
			return array_reverse($tables);
		}


		/**
		 * @return Diffs\UpdatedTable[]
		 */
		public function getUpdatedTables()
		{
			$tables = array();
			$tablesToUpdate = array();

			foreach ($this->old->getTables() as $oldTable) {
				$tablesToUpdate[$oldTable->getName()] = $oldTable;
			}

			foreach ($this->new->getTables() as $newTable) {
				$tableName = $newTable->getName();

				if (isset($tablesToUpdate[$tableName])) {
					$updatedTable = $this->generateUpdate($tablesToUpdate[$tableName], $newTable);

					if ($updatedTable) {
						$tables[] = $updatedTable;
					}
				}
			}

			return $tables;
		}


		/**
		 * @return Diffs\UpdatedTable|NULL
		 */
		private function generateUpdate(SqlSchema\Table $old, SqlSchema\Table $new)
		{
			$updates = array();

			$this->generateColumnUpdates($updates, $old, $new);
			$this->generateIndexUpdates($updates, $old, $new);
			$this->generateForeignKeyUpdates($updates, $old, $new);
			$this->generateOptionUpdates($updates, $old, $new);
			$this->generateCommentUpdate($updates, $old, $new);

			if (!empty($updates)) {
				return new Diffs\UpdatedTable($new->getName(), $updates);
			}

			return NULL;
		}


		/**
		 * @return void
		 */
		private function generateColumnUpdates(array &$updates, SqlSchema\Table $old, SqlSchema\Table $new)
		{
			$oldColumns = array();
			$lastColumnName = NULL;
			$lastUpdatedColumnName = NULL;
			$oldPositions = array();

			foreach ($old->getColumns() as $column) {
				$columnName = $column->getName();
				$oldColumns[$columnName] = $column;
				$oldPositions[$columnName] = $lastColumnName;
				$lastColumnName = $columnName;
			}

			$lastColumnName = NULL;

			foreach ($new->getColumns() as $column) {
				$columnName = $column->getName();

				if (!isset($oldColumns[$columnName])) {
					$updates[] = new Diffs\CreatedTableColumn($new->getName(), $column, $lastColumnName);

				} else {
					if ($this->isTableColumnUpdated($oldColumns[$columnName], $column)) {
						$updates[] = new Diffs\UpdatedTableColumn($new->getName(), $column, $lastColumnName);

					} elseif ($oldPositions[$columnName] !== $lastUpdatedColumnName) {
						$updates[] = new Diffs\UpdatedTableColumn($new->getName(), $column, $lastColumnName, TRUE);
					}

					$lastUpdatedColumnName = $columnName;
				}

				unset($oldColumns[$columnName]);
				$lastColumnName = $columnName;
			}

			foreach ($oldColumns as $column) {
				$updates[] = new Diffs\RemovedTableColumn($new->getName(), $column->getName());
			}
		}


		/**
		 * @return void
		 */
		private function generateIndexUpdates(array &$updates, SqlSchema\Table $old, SqlSchema\Table $new)
		{
			$oldIndexes = array();

			foreach ($old->getIndexes() as $index) {
				$oldIndexes[$index->getName()] = $index;
			}

			foreach ($new->getIndexes() as $index) {
				$indexName = $index->getName();

				if (!isset($oldIndexes[$indexName])) {
					$updates[] = new Diffs\CreatedTableIndex($new->getName(), $index);

				} else {
					if ($this->isTableIndexUpdated($oldIndexes[$indexName], $index)) {
						$updates[] = new Diffs\UpdatedTableIndex($new->getName(), $index);
					}
				}

				unset($oldIndexes[$indexName]);
			}

			foreach ($oldIndexes as $index) {
				$updates[] = new Diffs\RemovedTableIndex($new->getName(), $index->getName());
			}
		}


		/**
		 * @return void
		 */
		private function generateForeignKeyUpdates(array &$updates, SqlSchema\Table $old, SqlSchema\Table $new)
		{
			$oldForeignKeys = array();

			foreach ($old->getForeignKeys() as $foreignKey) {
				$oldForeignKeys[$foreignKey->getName()] = $foreignKey;
			}

			foreach ($new->getForeignKeys() as $foreignKey) {
				$foreignKeyName = $foreignKey->getName();

				if (!isset($oldForeignKeys[$foreignKeyName])) {
					$updates[] = new Diffs\CreatedForeignKey($new->getName(), $foreignKey);

				} else {
					if ($this->isForeignKeyUpdated($oldForeignKeys[$foreignKeyName], $foreignKey)) {
						$updates[] = new Diffs\UpdatedForeignKey($new->getName(), $foreignKey);
					}
				}

				unset($oldForeignKeys[$foreignKeyName]);
			}

			foreach ($oldForeignKeys as $foreignKey) {
				$updates[] = new Diffs\RemovedForeignKey($new->getName(), $foreignKey->getName());
			}
		}


		/**
		 * @return void
		 */
		private function generateOptionUpdates(array &$updates, SqlSchema\Table $old, SqlSchema\Table $new)
		{
			$oldOptions = $old->getOptions();

			foreach ($new->getOptions() as $option => $value) {
				if (!array_key_exists($option, $oldOptions)) {
					$updates[] = new Diffs\AddedTableOption($new->getName(), $option, $value);

				} else {
					if ($oldOptions[$option] !== $value) {
						$updates[] = new Diffs\UpdatedTableOption($new->getName(), $option, $value);
					}
				}

				unset($oldOptions[$option]);
			}

			foreach ($oldOptions as $option => $value) {
				$updates[] = new Diffs\RemovedTableOption($new->getName(), $option);
			}
		}


		/**
		 * @return void
		 */
		private function generateCommentUpdate(array &$updates, SqlSchema\Table $old, SqlSchema\Table $new)
		{
			if ($old->getComment() !== $new->getComment()) {
				$updates[] = new Diffs\UpdatedTableComment($new->getName(), (string) $new->getComment());
			}
		}


		/**
		 * @return bool
		 */
		private function isTableColumnUpdated(SqlSchema\Column $old, SqlSchema\Column $new)
		{
			if ($old->getType() !== $new->getType()) {
				return TRUE;
			}

			if ($old->getParameters() !== $new->getParameters()) {
				return TRUE;
			}

			$oldOptions = $old->getOptions();
			$newOptions = $new->getOptions();
			ksort($oldOptions, SORT_STRING);
			ksort($newOptions, SORT_STRING);

			if ($oldOptions !== $newOptions) {
				return TRUE;
			}

			if ($old->isNullable() !== $new->isNullable()) {
				return TRUE;
			}

			if ($old->isAutoIncrement() !== $new->isAutoIncrement()) {
				return TRUE;
			}

			if ($old->getDefaultValue() !== $new->getDefaultValue()) {
				return TRUE;
			}

			if ($old->getComment() !== $new->getComment()) {
				return TRUE;
			}

			return FALSE;
		}


		/**
		 * @return bool
		 */
		private function isTableIndexUpdated(SqlSchema\Index $old, SqlSchema\Index $new)
		{
			if ($old->getType() !== $new->getType()) {
				return TRUE;
			}

			$oldColumns = $old->getColumns();
			$newColumns = $new->getColumns();

			if (count($oldColumns) !== count($newColumns)) {
				return TRUE;
			}

			foreach ($newColumns as $i => $newColumn) {
				if (!isset($oldColumns[$i])) {
					return TRUE;
				}

				$oldColumn = $oldColumns[$i];

				if ($oldColumn->getName() !== $newColumn->getName()) {
					return TRUE;
				}

				if ($oldColumn->getOrder() !== $newColumn->getOrder()) {
					return TRUE;
				}

				if ($oldColumn->getLength() !== $newColumn->getLength()) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @return bool
		 */
		private function isForeignKeyUpdated(SqlSchema\ForeignKey $old, SqlSchema\ForeignKey $new)
		{
			if ($old->getColumns() !== $new->getColumns()) {
				return TRUE;
			}

			if ($old->getTargetTable() !== $new->getTargetTable()) {
				return TRUE;
			}

			if ($old->getTargetColumns() !== $new->getTargetColumns()) {
				return TRUE;
			}

			if ($old->getOnUpdateAction() !== $new->getOnUpdateAction()) {
				return TRUE;
			}

			if ($old->getOnDeleteAction() !== $new->getOnDeleteAction()) {
				return TRUE;
			}

			return FALSE;
		}


		private function sortTables(array $allTables, array $tablesToSort)
		{
			$tableOrder = $this->resolveOrder($allTables);

			usort($tablesToSort, function ($a, $b) use ($tableOrder) {
				$orderA = $this->getTableOrder($tableOrder, $a);
				$orderB = $this->getTableOrder($tableOrder, $b);

				if ($orderA === $orderB) {
					return 0;
				}

				return $orderA > $orderB ? 1 : -1;
			});

			return $tablesToSort;
		}


		/**
		 * @return array  [tableName => order]
		 */
		private function resolveOrder(array $tables)
		{
			$resolver = new \Cz\Dependency;
			$tablesToSort = array();

			foreach ($tables as $table) {
				$sourceTable = $table->getName();
				$targetTables = array();

				foreach ($table->getForeignKeys() as $foreignKey) {
					$targetTables[] = $foreignKey->getTargetTable();
				}

				$tablesToSort[$sourceTable] = $targetTables;
			}

			ksort($tablesToSort, SORT_STRING);

			foreach ($tablesToSort as $sourceTable => $targetTables) {
				$resolver->add($sourceTable, $targetTables);
			}

			$order = $resolver->getResolved();
			return array_flip($order);
		}


		/**
		 * @param  array
		 * @param  Diffs\CreatedTable|Diffs\UpdatedTable|Diffs\RemovedTable
		 * @return int|NULL
		 */
		private function getTableOrder(array $tableOrder, $diff)
		{
			if (!($diff instanceof Diffs\CreatedTable) && !($diff instanceof Diffs\UpdatedTable) && !($diff instanceof Diffs\RemovedTable)) {
				throw new UnsupportedException('Diff ' . get_class($diff) . ' is not supported.');
			}

			$name = $diff->getTableName();
			return isset($tableOrder[$name]) ? $tableOrder[$name] : NULL;
		}
	}
