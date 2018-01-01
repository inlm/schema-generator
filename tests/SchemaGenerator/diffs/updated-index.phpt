<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->addIndex('name', 'INDEX', 'name');

	$new->addTable('book')
		->addIndex('name', 'UNIQUE', 'name');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nDROP INDEX `name`,\nADD UNIQUE KEY `name` (`name`);\n", $generator->dumper->getSql());
});
