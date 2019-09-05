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
		->addColumn('name', 'VARCHAR', [200])
			->setDefaultValue('XYZ');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("ALTER TABLE `book`\nADD COLUMN `name` VARCHAR(200) NOT NULL DEFAULT 'XYZ';\n", $generator->dumper->getSql());
});
