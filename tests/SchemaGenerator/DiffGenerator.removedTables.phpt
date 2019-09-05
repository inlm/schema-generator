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
		'book',
		'author',
	);

	foreach ($diff->getRemovedTables() as $k => $removedTable) {
		Assert::same($expected[$k], $removedTable->getTableName());
	}
});


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book');
	$old->addTable('author');
	$old->addTable('tag');

	$oldBookTag = $old->addTable('book_tag');
	$oldBookTag->addForeignKey('fk_book_tag_book', array('book_id'), 'book', 'id');
	$oldBookTag->addForeignKey('fk_book_tag_tag', array('tag_id'), 'tag', 'id');

	$new->addTable('tag');

	$diff = new DiffGenerator($old, $new);
	$expected = array(
		'book_tag',
		'book',
		'author',
	);

	foreach ($diff->getRemovedTables() as $k => $removedTable) {
		Assert::same($expected[$k], $removedTable->getTableName());
	}
});
