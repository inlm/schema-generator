<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$new->addTable('book')
		->addColumn('name', 'VARCHAR', array(200))
			->setDefaultValue('XYZ');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\n" . implode("\n", array(
		'CREATE TABLE `book` (',
		"\t`name` VARCHAR(200) NOT NULL DEFAULT 'XYZ'",
		');',
	)) . "\n", $generator->dumper->getSql());
});
