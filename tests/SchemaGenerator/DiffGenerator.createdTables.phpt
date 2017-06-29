<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('tag');

	$new->addTable('book');
	$new->addTable('author');
	$new->addTable('tag');

	$diff = new DiffGenerator($old, $new);
	$expected = array(
		'book',
		'author',
	);

	foreach ($diff->getCreatedTables() as $k => $createdTable) {
		$definition = $createdTable->getDefinition();
		Assert::true($definition instanceof SqlSchema\Table);
		Assert::same($expected[$k], $definition->getName());
	}
});
