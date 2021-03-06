<?php

	namespace Test;

	use CzProject;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator;


	class TestGenerator
	{
		public $logger;
		public $extractor;
		public $adapter;
		public $dumper;
		public $generator;


		public static function create(SqlSchema\Schema $oldSchema = NULL, SqlSchema\Schema $newSchema = NULL)
		{
			$oldSchema = $oldSchema ? $oldSchema : new SqlSchema\Schema;
			$newSchema = $newSchema ? $newSchema : new SqlSchema\Schema;
			$test = new static;
			$test->adapter = new DummyAdapter(new SchemaGenerator\Configuration($oldSchema));
			$test->extractor = new DummyExtractor($newSchema);
			$test->dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper;
			$test->dumper->setHeader([]);
			$test->logger = new CzProject\Logger\MemoryLogger;
			$test->generator = new SchemaGenerator\SchemaGenerator($test->extractor, $test->adapter, $test->dumper, $test->logger, SchemaGenerator\Database::MYSQL);

			return $test;
		}
	}
