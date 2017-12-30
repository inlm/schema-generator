<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$directory = __DIR__ . '/lm-invalid-table-option';
$adapter = new Test\DummyAdapter(new SchemaGenerator\Configuration(new SqlSchema\Schema));
$extractor = new SchemaGenerator\Extractors\LeanMapperExtractor($directory, new LeanMapper\DefaultMapper);
$dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper(new CzProject\SqlGenerator\Drivers\MysqlDriver);
$logger = new SchemaGenerator\Loggers\MemoryLogger;

$schemaGenerator = new SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);

Assert::exception(function () use ($schemaGenerator) {
	$schemaGenerator->generate();
}, 'Inlm\SchemaGenerator\EmptyException', "Empty definition of '@schemaOption'.");
