<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\Logger\ILogger;
	use CzProject\SqlSchema;


	class SchemaGenerator
	{
		/** @var IExtractor */
		private $extractor;

		/** @var IAdapter */
		private $adapter;

		/** @var IDumper */
		private $dumper;

		/** @var ILogger|NULL */
		private $logger;

		/** @var string */
		private $databaseType;

		/** @var array<string, string> */
		private $options;

		/** @var array<lowercase-string, DataType> */
		private $customTypes;

		/** @var bool */
		private $testMode = FALSE;

		/** @var bool|NULL */
		private $positionChanges = NULL;


		/**
		 * @param string $databaseType
		 */
		public function __construct(IExtractor $extractor, IAdapter $adapter, IDumper $dumper, ILogger $logger = NULL, $databaseType = Database::MYSQL)
		{
			$this->extractor = $extractor;
			$this->adapter = $adapter;
			$this->dumper = $dumper;
			$this->logger = $logger;
			$this->databaseType = $databaseType;
			$this->prepareDefaults($databaseType);
		}


		/**
		 * @param  string $name
		 * @param  string $dbType
		 * @param  int|float|string|array<int|float|string>|NULL $dbParameters
		 * @param  array<string|int, scalar|NULL> $dbOptions
		 * @return static
		 */
		public function setCustomType($name, $dbType, $dbParameters = [], array $dbOptions = [])
		{
			if (!is_array($dbParameters) && $dbParameters !== NULL) {
				$dbParameters = [$dbParameters];
			}

			$this->customTypes[strtolower($name)] = new DataType($dbType, $dbParameters, $dbOptions);
			return $this;
		}


		/**
		 * @param  string $option
		 * @param  string|NULL $value
		 * @return static
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
		 * @param  string $option
		 * @return static
		 */
		public function removeOption($option)
		{
			unset($this->options[$option]);
			return $this;
		}


		/**
		 * @param  bool $testMode
		 * @return static
		 */
		public function setTestMode($testMode = TRUE)
		{
			$this->testMode = $testMode;
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
		 * @param  string|NULL $description
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
			$configNew = new Configuration($this->extractor->generateSchema($options, $this->customTypes, $this->databaseType));
			$configNew->setOptions($options);

			$this->log('Generating diff');
			$schemaDiff = new DiffGenerator($configOld->getSchema(), $configNew->getSchema());

			$this->log('Generating migrations');
			$this->dumper->start($this->databaseType, $description);
			$positionChanges = $this->positionChanges;
			$dumperPositionChanges = FALSE;

			if ($positionChanges === NULL) {
				if ($this->dumper instanceof Dumpers\AbstractSqlDumper) {
					$positionChanges = $this->dumper->hasEnabledPositionChanges();

				} else {
					$positionChanges = FALSE;
				}

			} elseif ($this->dumper instanceof Dumpers\AbstractSqlDumper) {
				$dumperPositionChanges = $this->dumper->hasEnabledPositionChanges();
				$this->dumper->enablePositionChanges($positionChanges);
			}

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
						if ($column->hasOnlyPositionChange()) {
							if (!$positionChanges) {
								continue;
							}

							$this->log(" - moved column {$column->getTableName()}.{$column->getDefinition()->getName()}");
							$this->dumper->updateTableColumn($column);

						} else {
							$this->log(" - updated column {$column->getTableName()}.{$column->getDefinition()->getName()}");
							$this->dumper->updateTableColumn($column);
						}
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

			if ($this->dumper instanceof Dumpers\AbstractSqlDumper) {
				$this->dumper->enablePositionChanges($dumperPositionChanges);
			}

			if (!$this->testMode) {
				$this->log('Saving schema');
				$this->adapter->save($configNew);
			}

			$this->log('Done.');
		}


		/**
		 * @param  string $databaseType
		 * @return void
		 */
		private function prepareDefaults($databaseType)
		{
			if ($databaseType === Database::MYSQL) {
				$this->options = [
					'ENGINE' => 'InnoDB',
					'CHARACTER SET' => 'utf8mb4',
					'COLLATE' => 'utf8mb4_czech_ci',
				];

				$this->setCustomType('positive-int', 'INT', [], [SqlSchema\Column::OPTION_UNSIGNED]);
				$this->setCustomType('negative-int', 'INT');
				$this->setCustomType('non-positive-int', 'INT');
				$this->setCustomType('non-negative-int', 'INT', [], [SqlSchema\Column::OPTION_UNSIGNED]);
				$this->setCustomType('non-zero-int', 'INT');
				$this->setCustomType('lowercase-string', 'TEXT');
				$this->setCustomType('literal-string', 'TEXT');
				$this->setCustomType('class-string', 'TEXT');
				$this->setCustomType('interface-string', 'TEXT');
				$this->setCustomType('trait-string', 'TEXT');
				$this->setCustomType('enum-string', 'TEXT');
				$this->setCustomType('callable-string', 'TEXT');
				$this->setCustomType('array-key', 'TEXT');
				$this->setCustomType('numeric-string', 'TEXT');
				$this->setCustomType('non-empty-string', 'TEXT');
				$this->setCustomType('non-empty-lowercase-string', 'TEXT');
				$this->setCustomType('truthy-string', 'TEXT');
				$this->setCustomType('non-falsy-string', 'TEXT');
				$this->setCustomType('non-empty-literal-string', 'TEXT');

				$this->setCustomType('bcrypt', 'CHAR', [60]);
				$this->setCustomType('md5', 'CHAR', [32]);
				$this->setCustomType('money', 'DECIMAL', [15, 4]);
				$this->setCustomType(\DateInterval::class, 'TIME');
				$this->setCustomType(\Inteve\Types\HexColor::class, 'CHAR', [6]);
				$this->setCustomType(\Inteve\Types\Html::class, 'MEDIUMTEXT');
				$this->setCustomType(\Inteve\Types\Md5Hash::class, 'CHAR', [32]);
				$this->setCustomType(\Inteve\Types\Password::class, 'VARCHAR', [255]); // https://www.php.net/manual/en/function.password-hash.php#refsect1-function.password-hash-description
				$this->setCustomType(\Inteve\Types\UniqueId::class, 'CHAR', [10]);
				$this->setCustomType(\Inteve\Types\Url::class, 'VARCHAR', [255]);
				$this->setCustomType(\Inteve\Types\UrlSlug::class, 'VARCHAR', [255]);
				$this->setCustomType(\Inteve\Types\UrlPath::class, 'VARCHAR', [255]);
			}
		}


		/**
		 * @param  string $msg
		 * @return void
		 */
		private function log($msg)
		{
			if (isset($this->logger)) {
				$this->logger->log($msg);
			}
		}
	}
