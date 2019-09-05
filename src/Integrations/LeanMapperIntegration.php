<?php

	namespace Inlm\SchemaGenerator\Integrations;

	use Inlm\SchemaGenerator;
	use LeanMapper;


	class LeanMapperIntegration extends AbstractIntegration
	{
		/** @var string */
		private $schemaFile;

		/** @var string */
		private $migrationsDirectory;

		/** @var string|string[] */
		private $entityDirectories;

		/** @var array|NULL */
		private $options;

		/** @var array|NULL */
		private $customTypes;

		/** @var array */
		private $ignoredTables;

		/** @var LeanMapper\Connection */
		private $connection;

		/** @var LeanMapper\IMapper */
		private $mapper;


		/**
		 * @param  string
		 * @param  string
		 * @param  string|string[]
		 * @param  array|NULL
		 * @param  array|NULL
		 * @param  array
		 */
		public function __construct(
			$schemaFile,
			$migrationsDirectory,
			$entityDirectories,
			array $options = NULL,
			array $customTypes = NULL,
			array $ignoredTables = [],
			LeanMapper\Connection $connection,
			LeanMapper\IMapper $mapper
		)
		{
			$this->schemaFile = $schemaFile;
			$this->migrationsDirectory = $migrationsDirectory;
			$this->entityDirectories = $entityDirectories;
			$this->options = $options;
			$this->customTypes = $customTypes;
			$this->ignoredTables = $ignoredTables;
			$this->connection = $connection;
			$this->mapper = $mapper;
		}


		protected function getOptions()
		{
			return $this->options;
		}


		protected function getCustomTypes()
		{
			return $this->customTypes;
		}


		protected function createExtractor()
		{
			return new SchemaGenerator\Extractors\LeanMapperExtractor($this->entityDirectories, $this->mapper);
		}


		protected function createAdapter()
		{
			return new SchemaGenerator\Adapters\NeonAdapter($this->schemaFile);
		}


		protected function createDatabaseExtractor()
		{
			return new SchemaGenerator\Extractors\DibiExtractor($this->connection, $this->ignoredTables);
		}


		protected function createDatabaseAdapter()
		{
			return new SchemaGenerator\Adapters\DibiAdapter($this->connection, $this->ignoredTables);
		}


		protected function createDatabaseDumper()
		{
			return new SchemaGenerator\Dumpers\DibiDumper($this->connection);
		}


		protected function createSqlDumper()
		{
			$dumper = new SchemaGenerator\Dumpers\SqlDumper($this->migrationsDirectory);
			$dumper->setOutputStructure($dumper::YEAR_MONTH);
			return $dumper;
		}


		protected function createLogger()
		{
			return new SchemaGenerator\Loggers\OutputLogger;
		}
	}
