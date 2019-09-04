<?php

	namespace Inlm\SchemaGenerator\Extractors;

	use CzProject\SqlSchema;
	use Dibi;
	use Inlm\SchemaGenerator\Bridges;
	use Inlm\SchemaGenerator\IExtractor;
	use Inlm\SchemaGenerator\Utils\Generator;
	use Inlm\SchemaGenerator\Utils\DataTypeParser;
	use Nette;


	class DibiExtractor implements IExtractor
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
		 * @return SqlSchema\Schema
		 */
		public function generateSchema(array $options = array(), array $customTypes = array(), $databaseType = NULL)
		{
			$dibiDriver = $this->connection->getDriver();

			if (Bridges\Dibi::isMysqlDriver($dibiDriver)) {
				$generator = new Bridges\DibiMysql($this->connection);
				return $generator->generateSchema($this->ignoredTables);
			}

			throw new \Inlm\SchemaGenerator\UnsupportedException('Driver ' . get_class($dibiDriver) . ' is not supported.');
		}
	}
