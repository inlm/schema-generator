<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/lm-sti/Mapper.php';

$directory = __DIR__ . '/lm-sti';
$adapter = new Test\DummyAdapter(new SchemaGenerator\Configuration(new SqlSchema\Schema));
$extractor = new SchemaGenerator\Extractors\LeanMapperExtractor($directory, new Mapper);
$dumper = new SchemaGenerator\Dumpers\SqlMemoryDumper(new CzProject\SqlGenerator\Drivers\MysqlDriver);
$logger = new CzProject\Logger\MemoryLogger;

$schemaGenerator = new SchemaGenerator\SchemaGenerator($extractor, $adapter, $dumper, $logger);
$schemaGenerator->generate();

Assert::matchFile($directory . '/dump-mysql.sql', $dumper->getSql());

Assert::same([
	'Generating schema',
	'Generating diff',
	'Generating migrations',
	' - created table client',
	'Saving schema',
	'Done.',
], $logger->getLog());
