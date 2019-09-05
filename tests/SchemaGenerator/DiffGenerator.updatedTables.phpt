<?php

use CzProject\SqlSchema;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$diff = Test\Diff::create();
$updatedTables = $diff->getUpdatedTables();

/**
 * Created columns
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getCreatedColumns() as $createdColumn) {
			$definition = $createdColumn->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $createdColumn->getTableName(),
				'column' => $definition->getName(),
				'afterColumn' => $createdColumn->getAfterColumn(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\Column',
			'table' => 'author',
			'column' => 'website',
			'afterColumn' => 'name',
		],
	], $result);
});

/**
 * Updated columns
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getUpdatedColumns() as $updatedColumn) {
			$definition = $updatedColumn->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $updatedColumn->getTableName(),
				'column' => $definition->getName(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\Column',
			'table' => 'author',
			'column' => 'name',
		],
	], $result);
});

/**
 * Removed columns
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getRemovedColumns() as $removedColumn) {
			$result[] = [
				'table' => $removedColumn->getTableName(),
				'column' => $removedColumn->getColumnName(),
			];
		}
	}

	Assert::same([
		[
			'table' => 'author',
			'column' => 'tag_id',
		],
	], $result);
});


/**
 * Created indexes
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getCreatedIndexes() as $createdIndex) {
			$definition = $createdIndex->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $createdIndex->getTableName(),
				'index' => $definition->getName(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\Index',
			'table' => 'author',
			'index' => 'website',
		],
	], $result);
});

/**
 * Updated indexes
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getUpdatedIndexes() as $updatedIndex) {
			$definition = $updatedIndex->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $updatedIndex->getTableName(),
				'index' => $definition->getName(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\Index',
			'table' => 'author',
			'index' => 'name',
		],
	], $result);
});

/**
 * Removed indexes
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getRemovedIndexes() as $removedIndex) {
			$result[] = [
				'table' => $removedIndex->getTableName(),
				'index' => $removedIndex->getIndexName(),
			];
		}
	}

	Assert::same([
		[
			'table' => 'author',
			'index' => 'tag_id',
		],
	], $result);
});


/**
 * Created FKs
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getCreatedForeignKeys() as $createdForeignKey) {
			$definition = $createdForeignKey->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $createdForeignKey->getTableName(),
				'fk' => $definition->getName(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\ForeignKey',
			'table' => 'author',
			'fk' => 'fk_section',
		],
	], $result);
});

/**
 * Updated FKs
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getUpdatedForeignKeys() as $updatedForeignKey) {
			$definition = $updatedForeignKey->getDefinition();

			$result[] = [
				'type' => get_class($definition),
				'table' => $updatedForeignKey->getTableName(),
				'index' => $definition->getName(),
			];
		}
	}

	Assert::same([
		[
			'type' => 'CzProject\SqlSchema\ForeignKey',
			'table' => 'author',
			'index' => 'fk_updated',
		],
	], $result);
});

/**
 * Removed FKs
 */
test(function () use ($updatedTables) {
	$result = [];

	foreach ($updatedTables as $updatedTable) {
		foreach ($updatedTable->getRemovedForeignKeys() as $removedForeignKey) {
			$result[] = [
				'table' => $removedForeignKey->getTableName(),
				'index' => $removedForeignKey->getForeignKeyName(),
			];
		}
	}

	Assert::same([
		[
			'table' => 'author',
			'index' => 'fk_tag',
		],
	], $result);
});
