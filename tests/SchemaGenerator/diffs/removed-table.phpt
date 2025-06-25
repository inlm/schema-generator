<?php

declare(strict_types=1);

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$old->addTable('book');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same("DROP TABLE `book`;\n", $generator->dumper->getSql());
});


test(function () { // circular dependency
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$oldGallery = $old->addTable('gallery');
	$oldGallery->addColumn('id', 'INT');
	$oldGallery->addColumn('primaryPhoto_id', 'INT');
	$oldGallery->addForeignKey('fk_primaryPhoto', 'primaryPhoto_id', 'photo', 'id');

	$oldPhoto = $old->addTable('photo');
	$oldPhoto->addColumn('id', 'INT');
	$oldPhoto->addColumn('gallery_id', 'INT');
	$oldPhoto->addForeignKey('fk_gallery', 'gallery_id', 'gallery', 'id');

	$newGallery = $new->addTable('gallery');
	$newGallery->addColumn('id', 'INT');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same(implode("\n", [
		'ALTER TABLE `gallery`',
		'DROP FOREIGN KEY `fk_primaryPhoto`,',
		'DROP COLUMN `primaryPhoto_id`;',
		'',
		"DROP TABLE `photo`;",
	]) . "\n", $generator->dumper->getSql());
});


test(function () { // circular dependency - self reference
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$oldGallery = $old->addTable('gallery');
	$oldGallery->addColumn('id', 'INT');
	$oldGallery->addColumn('parent_id', 'INT');
	$oldGallery->addForeignKey('fk_parent', 'parent_id', 'gallery', 'id');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same(implode("\n", [
		'ALTER TABLE `gallery`',
		'DROP FOREIGN KEY `fk_parent`;',
		'',
		"DROP TABLE `gallery`;",
	]) . "\n", $generator->dumper->getSql());
});
