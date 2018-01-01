<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->setOption('ENGINE', 'MyISAM');

	$new->addTable('book')
		->setOption('ENGINE', 'InnoDB');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("\nALTER TABLE `book`\nENGINE=InnoDB;\n", $generator->dumper->getSql());
});
