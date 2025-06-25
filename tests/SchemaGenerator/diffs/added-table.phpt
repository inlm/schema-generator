<?php

declare(strict_types=1);

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


test(function () { // circular dependency
	$old = new SqlSchema\Schema;
	$new = new SqlSchema\Schema;

	$gallery = $new->addTable('gallery');
	$gallery->addColumn('id', 'INT');
	$gallery->addColumn('primaryPhoto_id', 'INT');
	$gallery->addForeignKey('fk_primaryPhoto', 'primaryPhoto_id', 'photo', 'id');

	$photo = $new->addTable('photo');
	$photo->addColumn('id', 'INT');
	$photo->addColumn('gallery_id', 'INT');
	$photo->addForeignKey('fk_gallery', 'gallery_id', 'gallery', 'id');

	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->generate();
	Assert::same(implode("\n", [
		'CREATE TABLE `photo` (',
		"\t`id` INT NOT NULL,",
		"\t`gallery_id` INT NOT NULL",
		');',
		'',
		'CREATE TABLE `gallery` (',
		"\t`id` INT NOT NULL,",
		"\t`primaryPhoto_id` INT NOT NULL,",
		"\tCONSTRAINT `fk_primaryPhoto` FOREIGN KEY (`primaryPhoto_id`) REFERENCES `photo` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT",
		');',
		'',
		'ALTER TABLE `photo`',
		"ADD CONSTRAINT `fk_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;",
	]) . "\n", $generator->dumper->getSql());
});
