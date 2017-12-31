<?php

	namespace Test;

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
			$test->dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper(SchemaGenerator\Dumpers\SqlMemoryDumper::MYSQL);
			$test->logger = new SchemaGenerator\Loggers\MemoryLogger;
			$test->generator = new SchemaGenerator\SchemaGenerator($test->extractor, $test->adapter, $test->dumper, $test->logger);

			return $test;
		}
	}
