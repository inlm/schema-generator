<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book');
	$old->addTable('author');
	$old->addTable('tag');

	$new->addTable('tag');

	$diff = new DiffGenerator($old, $new);
	$expected = array(
		'author',
		'book',
	);

	foreach ($diff->getRemovedTables() as $k => $removedTable) {
		Assert::same($expected[$k], $removedTable->getTableName());
	}
});
