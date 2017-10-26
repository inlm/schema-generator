<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;


	class DibiDumper extends AbstractSqlDumper
	{
		/** @var \DibiConnection|\Dibi\Connection */
		private $connection;


		/**
		 * @param  \DibiConnection|\Dibi\Connection
		 */
		public function __construct($connection)
		{
			if (!($connection instanceof \Dibi\Connection || $connection instanceof \DibiConnection)) {
				throw new \Inlm\SchemaGenerator\InvalidArgumentException('Connection must be instance of Dibi\Connection or DibiConnection.');
			}
			$this->connection = $connection;
		}


		/**
		 * @return void
		 */
		public function end()
		{
			$this->checkIfStarted();

			if (!$this->sqlDocument->isEmpty()) {
				$dibiDriver = $this->connection->getDriver();
				$sqlDriver = NULL;

				if ($dibiDriver instanceof \Dibi\Drivers\MySqlDriver || $dibiDriver instanceof DibiMySqlDriver) {
					$sqlDriver = new SqlGenerator\Drivers\MysqlDriver;

				} elseif ($dibiDriver instanceof \Dibi\Drivers\MySqliDriver || $dibiDriver instanceof DibiMySqliDriver) {
					$sqlDriver = new SqlGenerator\Drivers\MysqlDriver;

				} else {
					throw new \Inlm\SchemaGenerator\UnsupportedException('Driver ' . get_class($dibiDriver) . ' is not supported.');
				}

				$queries = $this->sqlDocument->getSqlQueries($sqlDriver);

				foreach ($queries as $query) {
					$dibiDriver->query($query);
				}
			}

			$this->stop();
		}
	}
