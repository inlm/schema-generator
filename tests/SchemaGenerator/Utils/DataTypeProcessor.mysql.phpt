<?php

use CzProject\SqlSchema\Column;
use Inlm\SchemaGenerator\Database;
use Inlm\SchemaGenerator\Utils\DataTypeProcessor;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


Assert::same([
	'type' => 'INT',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('int', NULL, FALSE, [], Database::MYSQL)));


Assert::same([
	'type' => 'INT',
	'parameters' => [],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('int', NULL, TRUE, [], Database::MYSQL)));


Assert::same([
	'type' => 'INT',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('integer', NULL, FALSE, [], Database::MYSQL)));


Assert::same([
	'type' => 'INT',
	'parameters' => [],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('integer', NULL, TRUE, [], Database::MYSQL)));


// bools
Assert::same([
	'type' => 'TINYINT',
	'parameters' => [1],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('bool', NULL, FALSE, [], Database::MYSQL)));


Assert::same([
	'type' => 'TINYINT',
	'parameters' => [1],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('boolean', NULL, FALSE, [], Database::MYSQL)));


// floats
Assert::same([
	'type' => 'DOUBLE',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('float', NULL, FALSE, [], Database::MYSQL)));


Assert::same([
	'type' => 'DOUBLE',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('double', NULL, FALSE, [], Database::MYSQL)));


// strings
Assert::same([
	'type' => 'TEXT',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('string', NULL, FALSE, [], Database::MYSQL)));


// datetimes
Assert::same([
	'type' => 'DATETIME',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process(\DateTime::class, NULL, FALSE, [], Database::MYSQL)));

Assert::same([
	'type' => 'DATETIME',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process(\DateTimeInterface::class, NULL, FALSE, [], Database::MYSQL)));

Assert::same([
	'type' => 'DATETIME',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process(\DateTimeImmutable::class, NULL, FALSE, [], Database::MYSQL)));
