<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema;


	class SchemaGenerator
	{
		/** @var IExtractor */
		private $extractor;

		/** @var IAdapter */
		private $adapter;

		/** @var IDumper */
		private $dumper;

		/** @var ILogger */
		private $logger;

		/** @var array */
		private $options;

		/** @var array */
		private $customTypes;

		/** @var bool */
		private $testMode = FALSE;


		public function __construct(IExtractor $extractor, IAdapter $adapter, IDumper $dumper, ILogger $logger = NULL)
		{
			$this->extractor = $extractor;
			$this->adapter = $adapter;
			$this->dumper = $dumper;
			$this->logger = $logger;
			$this->options = array(
				'ENGINE' => 'InnoDB',
				'CHARACTER SET' => 'utf8mb4',
				'COLLATE' => 'utf8mb4_czech_ci',
			);
			$this->setCustomType('money', 'DECIMAL', array(15, 4));
		}


		/**
		 * @param  string
		 * @param  string
		 * @param  scalar|scalar[]
		 * @param  array
		 * @return self
		 */
		public function setCustomType($name, $dbType, $dbParameters = array(), array $dbOptions = array())
		{
			$this->customTypes[strtolower($name)] = new DataType($dbType, $dbParameters, $dbOptions);
			return $this;
		}


		/**
		 * @param  string
		 * @param  scalar|NULL
		 * @return self
		 */
		public function setOption($option, $value)
		{
			if ($value === NULL) {
				$this->removeOption($option);

			} else {
				$this->options[$option] = $value;
			}

			return $this;
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function removeOption($option)
		{
			unset($this->options[$option]);
			return $this;
		}


		/**
		 * @param  bool
		 * @return self
		 */
		public function setTestMode($testMode = TRUE)
		{
			$this->testMode = $testMode;
			return $this;
		}


		/**
		 * @param  string|NULL
		 * @return void
		 */
		public function generate($description = NULL)
		{
			if ($this->testMode) {
				$this->log('TEST MODE');
			}

			$configOld = $this->adapter->load();
			$options = $configOld->getOptions() + $this->options;
			$this->log('Generating schema');
			$configNew = new Configuration($this->extractor->generateSchema($options, $this->customTypes));
			$configNew->setOptions($options);

			$this->log('Generating diff');
			$schemaDiff = new DiffGenerator($configOld->getSchema(), $configNew->getSchema());

			$this->log('Generating migrations');
			$this->dumper->start($description);

			foreach ($schemaDiff->getCreatedAndUpdatedTables() as $diff) {
				if ($diff instanceof Diffs\CreatedTable) {
					$this->log(" - created table {$diff->getTableName()}");
					$this->dumper->createTable($diff);

				} elseif ($diff instanceof Diffs\UpdatedTable) {
					// create
					foreach ($diff->getCreatedColumns() as $column) {
						$this->log(" - created column {$column->getTableName()}.{$column->getDefinition()->getName()}");
						$this->dumper->createTableColumn($column);
					}

					foreach ($diff->getCreatedIndexes() as $index) {
						$this->log(" - created index {$index->getTableName()}.{$index->getDefinition()->getName()}");
						$this->dumper->createTableIndex($index);
					}

					foreach ($diff->getCreatedForeignKeys() as $foreignKey) {
						$this->log(" - created foreign key {$foreignKey->getTableName()}.{$foreignKey->getDefinition()->getName()}");
						$this->dumper->createForeignKey($foreignKey);
					}

					foreach ($diff->getAddedOptions() as $option) {
						$this->log(" - added option {$option->getTableName()}.{$option->getOption()}");
						$this->dumper->addTableOption($option);
					}

					// update
					foreach ($diff->getUpdatedColumns() as $column) {
						$this->log(" - updated column {$column->getTableName()}.{$column->getDefinition()->getName()}");
						$this->dumper->updateTableColumn($column);
					}

					foreach ($diff->getUpdatedIndexes() as $index) {
						$this->log(" - updated index {$index->getTableName()}.{$index->getDefinition()->getName()}");
						$this->dumper->updateTableIndex($index);
					}

					foreach ($diff->getUpdatedForeignKeys() as $foreignKey) {
						$this->log(" - updated foreign key {$foreignKey->getTableName()}.{$foreignKey->getDefinition()->getName()}");
						$this->dumper->updateForeignKey($foreignKey);
					}

					foreach ($diff->getUpdatedOptions() as $option) {
						$this->log(" - updated option {$option->getTableName()}.{$option->getOption()}");
						$this->dumper->updateTableOption($option);
					}

					foreach ($diff->getUpdatedComments() as $comment) {
						$this->log(" - updated comment for {$comment->getTableName()}");
						$this->dumper->updateTableComment($comment);
					}

					// remove
					foreach ($diff->getRemovedForeignKeys() as $foreignKey) {
						$this->log(" - REMOVED foreign key {$foreignKey->getTableName()}.{$foreignKey->getForeignKeyName()}");
						$this->dumper->removeForeignKey($foreignKey);
					}

					foreach ($diff->getRemovedIndexes() as $index) {
						$this->log(" - REMOVED index {$index->getTableName()}.{$index->getIndexName()}");
						$this->dumper->removeTableIndex($index);
					}

					foreach ($diff->getRemovedColumns() as $column) {
						$this->log(" - REMOVED column {$column->getTableName()}.{$column->getColumnName()}");
						$this->dumper->removeTableColumn($column);
					}

					foreach ($diff->getRemovedOptions() as $option) {
						$this->log(" - REMOVED option {$option->getTableName()}.{$option->getOption()}");
						$this->dumper->removeTableOption($option);
					}

				} else {
					throw new UnsupportedException('Diff ' . get_class($diff) . ' is not supported.');
				}
			}

			foreach ($schemaDiff->getRemovedTables() as $removedTable) {
				$this->log(" - REMOVED table {$removedTable->getTableName()}");
				$this->dumper->removeTable($removedTable);
			}

			$this->dumper->end();

			if (!$this->testMode) {
				$this->log('Saving schema');
				$this->adapter->save($configNew);
			}

			$this->log('Done.');
		}


		/**
		 * @param  string
		 * @return void
		 */
		private function log($msg)
		{
			if (isset($this->logger)) {
				$this->logger->log($msg);
			}
		}
	}
