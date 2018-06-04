<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book');

	$new->addTable('book')
		->addIndex('name', 'name', 'UNIQUE');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nADD UNIQUE KEY `name` (`name`);\n", $generator->dumper->getSql());
});
