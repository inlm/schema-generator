<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$directory = __DIR__ . '/lm-indexes';
$adapter = new Test\DummyAdapter(new SchemaGenerator\Configuration(new SqlSchema\Schema));
$extractor = new SchemaGenerator\Extractors\LeanMapperExtractor($directory, new LeanMapper\DefaultMapper);
$dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper(new CzProject\SqlGenerator\Drivers\MysqlDriver);
$logger = new SchemaGenerator\Loggers\MemoryLogger;

$schemaGenerator = new SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$schemaGenerator->generate();

Assert::matchFile($directory . '/dump-mysql.sql', $dumper->getSql());

Assert::same(implode("\n", array(
	'Generating schema',
	'Generating diff',
	'Generating migrations',
	' - created table book',
	'Saving schema',
	'Done.',
	'',
)), $logger->getLog());
