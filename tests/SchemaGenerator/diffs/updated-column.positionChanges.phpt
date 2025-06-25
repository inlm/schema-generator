<?php

declare(strict_types=1);

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\DiffGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$old = new SqlSchema\Schema;
$new = new SqlSchema\Schema;

$oldTable = $old->addTable('book');
$oldTable->addColumn('id', 'INT');
$oldTable->addColumn('group', 'INT');
$oldTable->addColumn('title', 'TEXT');
$oldTable->addColumn('updated', 'DATETIME');

$newTable = $new->addTable('book');
$newTable->addColumn('id', 'INT');
$newTable->addColumn('parent_id', 'INT');
$newTable->addColumn('group', 'INT');
$newTable->addColumn('type', 'TINYINT');
$newTable->addColumn('updated', 'DATETIME');
$newTable->addColumn('title', 'VARCHAR', [200]);


// with positions
test(function () use ($old, $new) {
	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->enablePositionChanges();
	$generator->generator->generate();

	Assert::same([
		'Generating schema',
		'Generating diff',
		'Generating migrations',
		' - created column book.parent_id',
		' - created column book.type',
		' - moved column book.updated',
		' - updated column book.title',
		'Saving schema',
		'Done.',
	], $generator->logger->getLog());

	Assert::same(implode("\n", [
		"ALTER TABLE `book`",
		"ADD COLUMN `parent_id` INT NOT NULL AFTER `id`,",
		"ADD COLUMN `type` TINYINT NOT NULL AFTER `group`,",
		"MODIFY COLUMN `updated` DATETIME NOT NULL AFTER `type`,",
		"MODIFY COLUMN `title` VARCHAR(200) NOT NULL AFTER `updated`;",
	]), trim($generator->dumper->getSql()));
});


// without positions
test(function () use ($old, $new) {
	$generator = Test\TestGenerator::create($old, $new);
	$generator->generator->enablePositionChanges(FALSE);
	$generator->generator->generate();

	Assert::same([
		'Generating schema',
		'Generating diff',
		'Generating migrations',
		' - created column book.parent_id',
		' - created column book.type',
		' - updated column book.title',
		'Saving schema',
		'Done.',
	], $generator->logger->getLog());

	Assert::same(implode("\n", [
		"ALTER TABLE `book`",
		"ADD COLUMN `parent_id` INT NOT NULL,",
		"ADD COLUMN `type` TINYINT NOT NULL,",
		"MODIFY COLUMN `title` VARCHAR(200) NOT NULL;",
	]), trim($generator->dumper->getSql()));
});



// @deprecated - with positions
test(function () use ($old, $new) {
	$generator = Test\TestGenerator::create($old, $new);
	$generator->dumper->enablePositionChanges();
	$generator->generator->generate();

	Assert::same([
		'Generating schema',
		'Generating diff',
		'Generating migrations',
		' - created column book.parent_id',
		' - created column book.type',
		' - moved column book.updated',
		' - updated column book.title',
		'Saving schema',
		'Done.',
	], $generator->logger->getLog());

	Assert::same(implode("\n", [
		"ALTER TABLE `book`",
		"ADD COLUMN `parent_id` INT NOT NULL AFTER `id`,",
		"ADD COLUMN `type` TINYINT NOT NULL AFTER `group`,",
		"MODIFY COLUMN `updated` DATETIME NOT NULL AFTER `type`,",
		"MODIFY COLUMN `title` VARCHAR(200) NOT NULL AFTER `updated`;",
	]), trim($generator->dumper->getSql()));
});


// @deprecated - without positions
test(function () use ($old, $new) {
	$generator = Test\TestGenerator::create($old, $new);
	$generator->dumper->enablePositionChanges(FALSE);
	$generator->generator->generate();

	Assert::same([
		'Generating schema',
		'Generating diff',
		'Generating migrations',
		' - created column book.parent_id',
		' - created column book.type',
		' - updated column book.title',
		'Saving schema',
		'Done.',
	], $generator->logger->getLog());

	Assert::same(implode("\n", [
		"ALTER TABLE `book`",
		"ADD COLUMN `parent_id` INT NOT NULL,",
		"ADD COLUMN `type` TINYINT NOT NULL,",
		"MODIFY COLUMN `title` VARCHAR(200) NOT NULL;",
	]), trim($generator->dumper->getSql()));
});
