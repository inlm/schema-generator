<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$book = $new->addTable('book');
	$book->addColumn('name', 'VARCHAR', [200])
		->setDefaultValue('XYZ');

	$book->addForeignKey('fk_author', 'author_id', 'author', 'id')
		->setOnUpdateAction('CASCADE')
		->setOnDeleteAction('NO ACTION');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same(implode("\n", [
		'CREATE TABLE `book` (',
		"\t`name` VARCHAR(200) NOT NULL DEFAULT 'XYZ',",
		"\tCONSTRAINT `fk_author` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE",
		');',
	]) . "\n", $generator->dumper->getSql());
});
