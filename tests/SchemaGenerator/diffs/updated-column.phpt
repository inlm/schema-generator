<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->addColumn('name', 'TEXT');

	$new->addTable('book')
		->addColumn('name', 'VARCHAR', array(200));

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nMODIFY COLUMN `name` VARCHAR(200) NOT NULL;\n", $generator->dumper->getSql());
});


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->addColumn('name', 'TEXT');

	$new->addTable('book')
		->addColumn('name', 'TEXT')
			->setDefaultValue('XYZ');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nMODIFY COLUMN `name` TEXT NOT NULL DEFAULT 'XYZ';\n", $generator->dumper->getSql());
});
