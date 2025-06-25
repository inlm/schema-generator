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
		->addForeignKey('fk_author', 'author_id', 'author', 'id');

	$new->addTable('book')
		->addForeignKey('fk_author', 'owner_id', 'author', 'id');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("ALTER TABLE `book`\nDROP FOREIGN KEY `fk_author`,\nADD CONSTRAINT `fk_author` FOREIGN KEY (`owner_id`) REFERENCES `author` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;\n", $generator->dumper->getSql());
});
