<?php

	namespace Inlm\SchemaGenerator\Adapters;

	use CzProject\SqlSchema\Schema;
	use Inlm\SchemaGenerator\Bridges;
	use Inlm\SchemaGenerator\Configuration;
	use Inlm\SchemaGenerator\IAdapter;


	class DibiAdapter implements IAdapter
	{
		/** @var \DibiConnection|\Dibi\Connection */
		private $connection;

		/** @var string[] */
		private $ignoredTables;


		/**
		 * @param  \DibiConnection|\Dibi\Connection
		 * @param  string[]
		 */
		public function __construct($connection, array $ignoredTables = array())
		{
			Bridges\Dibi::validateConnection($connection);
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
