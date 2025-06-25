<?php

declare(strict_types=1);

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book')
		->addIndex(NULL, 'id', SqlSchema\Index::TYPE_PRIMARY);

	$new->addTable('book')
		->addIndex(NULL, 'new_id', SqlSchema\Index::TYPE_PRIMARY);

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("ALTER TABLE `book`\nDROP PRIMARY KEY,\nADD PRIMARY KEY (`new_id`);\n", $generator->dumper->getSql());
});
