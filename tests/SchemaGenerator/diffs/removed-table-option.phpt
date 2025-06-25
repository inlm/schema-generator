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
		->setOption('ENGINE', 'InnoDB');

	$new->addTable('book');

	$generator = Test\TestGenerator::create($old, $new);

	Assert::exception(function () use ($generator) {
		$generator->generator->generate();
	}, 'Inlm\SchemaGenerator\UnsupportedException', 'Removing of table options is not supported.');
});
