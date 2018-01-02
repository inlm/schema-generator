<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/lm-sti/Mapper.php';

$directory = __DIR__ . '/lm-empty-properties';
$adapter = new Test\DummyAdapter(new SchemaGenerator\Configuration(new SqlSchema\Schema));
$extractor = new SchemaGenerator\Extractors\LeanMapperExtractor($directory, new LeanMapper\DefaultMapper);
$dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper(SchemaGenerator\Dumpers\SqlMemoryDumper::MYSQL);
$logger = new SchemaGenerator\Loggers\MemoryLogger;

$schemaGenerator = new SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$schemaGenerator->generate();

Assert::same('', $dumper->getSql());

Assert::same(implode("\n", array(
	'Generating schema',
	'Generating diff',
	'Generating migrations',
	'Saving schema',
	'Done.',
	'',
)), $logger->getLog());