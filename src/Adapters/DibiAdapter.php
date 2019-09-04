<?php

	namespace Inlm\SchemaGenerator\Adapters;

	use CzProject\SqlSchema\Schema;
	use Dibi;
	use Inlm\SchemaGenerator\Bridges;
	use Inlm\SchemaGenerator\Configuration;
	use Inlm\SchemaGenerator\IAdapter;


	class DibiAdapter implements IAdapter
	{
		/** @var Dibi\Connection */
		private $connection;

		/** @var string[] */
		private $ignoredTables;


		/**
		 * @param  Dibi\Connection
		 * @param  string[]
		 */
		public function __construct(Dibi\Connection $connection, array $ignoredTables = array())
		{
			$this->connection = $connection;
			$this->ignoredTables = $ignoredTables;
		}


		/**
		 * @return Configuration
		 */
		public function load()
		{
			$dibiDriver = $this->connection->getDriver();
			$schema = NULL;

			if (Bridges\Dibi::isMysqlDriver($dibiDriver)) {
				$generator = new Bridges\DibiMysql($this->connection);
				$schema = $generator->generateSchema($this->ignoredTables);

			} else {
				throw new \Inlm\SchemaGenerator\UnsupportedException('Driver ' . get_class($dibiDriver) . ' is not supported.');
			}

			return new Configuration($schema);
		}


		/**
		 * @return void
		 */
		public function save(Configuration $configuration)
		{
		}
	}
