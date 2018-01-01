<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->addForeignKey('name');

	$new->addTable('book');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nDROP FOREIGN KEY `name`;\n", $generator->dumper->getSql());
});
