<?php

	namespace Inlm\SchemaGenerator\Bridges;

	use CzProject\SqlSchema;
	use Dibi;
	use Inlm\SchemaGenerator\Utils\DataTypeParser;


	class DibiMysql
	{
		/** @var Dibi\Connection */
		private $connection;

		/** @var array  [tableName => (array) metadata] */
		private $tableMetas;


		public function __construct(Dibi\Connection $connection)
		{
			$this->connection = $connection;
		}


		/**
		 * @param  string[]
		 * @return SqlSchema\Schema
		 */
		public function generateSchema(array $ignoredTables = [])
		{
			$schema = new SqlSchema\Schema;

			foreach ($this->getTables() as $tableName) {
				if (in_array($tableName, $ignoredTables, TRUE)) {
					continue;
				}

				$schema->addTable($this->createTable($tableName));
			}

			return $schema;
		}


		private function getTables()
		{
			$rows = $this->connection->fetchAll('SHOW FULL TABLES');
			$tables = [];

			foreach ($rows as $row) {
				$data = $row->toArray();
				$tables[] = array_shift($data);
			}

			$this->tableMetas = $this->connection->query('SHOW TABLE STATUS')
				->fetchAssoc('Name,=');

			// character sets
			$rows = $this->connection->fetchAll('SELECT
				T.table_name,
				CCSA.character_set_name
				FROM
					INFORMATION_SCHEMA.`TABLES` AS T,
					INFORMATION_SCHEMA.`COLLATION_CHARACTER_SET_APPLICABILITY` AS CCSA
				WHERE
					CCSA.collation_name = T.table_collation
				AND T.table_schema = DATABASE();
			');

			foreach ($rows as $row) {
				$this->tableMetas[$row['table_name']]['CHARACTER SET'] = $row['character_set_name'];
			}

			return $tables;
		}


		private function createTable($name)
		{
			$table = new SqlSchema\Table($name);
			$this->assignTableMetaData($table);
			$rows = $this->connection->fetchAll('SHOW FULL COLUMNS FROM %n', $name);
			$meta = isset($this->tableMetas[$name]) ? $this->tableMetas[$name] : [];
			$columnsMeta = $this->getColumnsMeta($name);

			foreach ($rows as $row) {
				$rowMeta = isset($columnsMeta[$row['Field']]) ? $columnsMeta[$row['Field']] : [];
				$datatype = DataTypeParser::parse($row['Type']);
				$options = $datatype->getOptions();

				if ($row['Collation'] !== NULL) {
					if (!isset($meta['Collation']) || $meta['Collation'] !== $row['Collation']) {
						$options['COLLATE'] = $row['Collation'];
					}
				}

				if ($rowMeta['CHARACTER SET'] !== NULL) {
					if (!isset($meta['CHARACTER SET']) || $meta['CHARACTER SET'] !== $rowMeta['CHARACTER SET']) {
						$options['CHARACTER SET'] = $rowMeta['CHARACTER SET'];
					}
				}

				$column = $table->addColumn(
					$row['Field'],
					$datatype->getType(),
					$datatype->getParameters(),
					$options
				);
				$column->setNullable($row['Null'] === 'YES');
				$column->setDefaultValue($row['Default']);
				$column->setAutoIncrement($row['Extra'] === 'auto_increment');

				if ($row['Comment'] !== '') {
					$column->setComment($row['Comment']);
				}
			}

			$this->createTableIndexes($table);
			$this->createTableForeignKeys($table);
			return $table;
		}


		/**
		 * @return void
		 */
		private function assignTableMetaData(SqlSchema\Table $table)
		{
			$name = $table->getName();

			if (!isset($this->tableMetas[$name])) {
				return;
			}

			$meta = $this->tableMetas[$name];
			$options = [
				'Engine' => 'ENGINE',
				'CHARACTER SET' => 'CHARACTER SET',
				'Collation' => 'COLLATE',
			];

			foreach ($options as $key => $option) {
				if (isset($meta[$key])) {
					$table->setOption($option, $meta[$key]);
				}
			}

			if (isset($meta['Comment']) && $meta['Comment'] !== '') {
				$table->setComment($meta['Comment']);
			}
		}


		private function getColumnsMeta($tableName)
		{
			$rows = $this->connection->fetchAll('SELECT
				column_name,
				character_set_name
				FROM INFORMATION_SCHEMA.`COLUMNS`
				WHERE table_schema = DATABASE()
					AND table_name = %s', $tableName
			);
			$meta = [];

			foreach ($rows as $row) {
				$meta[$row['column_name']]['CHARACTER SET'] = $row['character_set_name'];
			}

			return $meta;
		}


		/**
		 * @return void
		 */
		private function createTableIndexes(SqlSchema\Table $table)
		{
			$rows = $this->connection->fetchAll('SHOW INDEXES FROM %n', $table->getName());
			$indexes = [];

			foreach ($rows as $row) {
				$name = $row['Key_name'];

				if ($name === 'PRIMARY') {
					$name = NULL;
				}

				if (!isset($indexes[$name])) {
					$type = SqlSchema\Index::TYPE_INDEX;

					if ($name === NULL) {
						$type = SqlSchema\Index::TYPE_PRIMARY;

					} elseif (!$row['Non_unique']) {
						$type = SqlSchema\Index::TYPE_UNIQUE;

					} elseif ($row['Index_type'] === 'FULLTEXT') {
						$type = SqlSchema\Index::TYPE_FULLTEXT;
					}

					$indexes[$name] = $table->addIndex($name, [], $type);
				}

				$index = $indexes[$name];
				$index->addColumn($row['Column_name'])
					->setOrder($row['Collation'] === 'A' ? SqlSchema\IndexColumn::ASC : SqlSchema\IndexColumn::DESC)
					->setLength($row['Sub_part']);
			}
		}


		/**
		 * @return void
		 */
		private function createTableForeignKeys(SqlSchema\Table $table)
		{
			$rows = $this->connection->fetchAll('SELECT
				KEY_COLUMN_USAGE.TABLE_NAME,
				KEY_COLUMN_USAGE.COLUMN_NAME,
				KEY_COLUMN_USAGE.CONSTRAINT_NAME,
				KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME,
				KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME,
				REFERENTIAL_CONSTRAINTS.UPDATE_RULE,
				REFERENTIAL_CONSTRAINTS.DELETE_RULE
				FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS REFERENTIAL_CONSTRAINTS
				INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KEY_COLUMN_USAGE
					ON KEY_COLUMN_USAGE.CONSTRAINT_NAME = REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME
					AND KEY_COLUMN_USAGE.TABLE_SCHEMA = REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA
					AND KEY_COLUMN_USAGE.TABLE_NAME = REFERENTIAL_CONSTRAINTS.TABLE_NAME
				WHERE
  					KEY_COLUMN_USAGE.TABLE_SCHEMA = DATABASE()
  					AND
					KEY_COLUMN_USAGE.TABLE_NAME = %s', $table->getName()
			);
			$fks = [];

			foreach ($rows as $row) {
				$name = $row['CONSTRAINT_NAME'];

				if (!isset($fks[$name])) {
					$fks[$name] = $table->addForeignKey($name, [], $row['REFERENCED_TABLE_NAME'], [])
						->setOnUpdateAction($row['UPDATE_RULE'])
						->setOnDeleteAction($row['DELETE_RULE']);
				}

				$fk = $fks[$name];
				$fk->addColumn($row['COLUMN_NAME']);
				$fk->addTargetColumn($row['REFERENCED_COLUMN_NAME']);
			}
		}
	}
