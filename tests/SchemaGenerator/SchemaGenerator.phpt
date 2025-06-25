<?php

declare(strict_types=1);

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\Dumpers;
use Inlm\SchemaGenerator\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$oldSchema = Test\Diff::createOldSchema();
	$newSchema = Test\Diff::createNewSchema();

	$oldSchema->addTable('roles');
	$newSchema->addTable('photos');

	$logger = new CzProject\Logger\MemoryLogger;
	$adapter = new Test\DummyAdapter(new Configuration($oldSchema));
	$extractor = new Test\DummyExtractor($newSchema);
	$dumper = new Dumpers\NullDumper;

	$schemaGenerator = new SchemaGenerator($extractor, $adapter, $dumper, $logger);
	$schemaGenerator->generate();

	Assert::same([
		'Generating schema',
		'Generating diff',
		'Generating migrations',
		' - created column author.website',
		' - created index author.website',
		' - created foreign key author.fk_section',
		' - updated column author.name',
		' - updated index author.name',
		' - updated foreign key author.fk_updated',
		' - REMOVED foreign key author.fk_tag',
		' - REMOVED index author.tag_id',
		' - REMOVED column author.tag_id',
		' - created table photos',
		' - REMOVED table roles',
		'Saving schema',
		'Done.',
	], $logger->getLog());
});
