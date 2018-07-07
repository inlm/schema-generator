<?php

use CzProject\SqlSchema\Column;
use Inlm\SchemaGenerator\Database;
use Inlm\SchemaGenerator\Utils\DataTypeProcessor;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


Assert::same(array(
	'type' => 'INT',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('int', NULL, FALSE, array(), Database::MYSQL)));


Assert::same(array(
	'type' => 'INT',
	'parameters' => array(),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('int', NULL, TRUE, array(), Database::MYSQL)));


Assert::same(array(
	'type' => 'INT',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('integer', NULL, FALSE, array(), Database::MYSQL)));


Assert::same(array(
	'type' => 'INT',
	'parameters' => array(),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('integer', NULL, TRUE, array(), Database::MYSQL)));


// bools
Assert::same(array(
	'type' => 'TINYINT',
	'parameters' => array(1),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('bool', NULL, FALSE, array(), Database::MYSQL)));


Assert::same(array(
	'type' => 'TINYINT',
	'parameters' => array(1),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('boolean', NULL, FALSE, array(), Database::MYSQL)));


// floats
Assert::same(array(
	'type' => 'DOUBLE',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('float', NULL, FALSE, array(), Database::MYSQL)));


Assert::same(array(
	'type' => 'DOUBLE',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('double', NULL, FALSE, array(), Database::MYSQL)));


// strings
Assert::same(array(
	'type' => 'TEXT',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('string', NULL, FALSE, array(), Database::MYSQL)));


// datetimes
Assert::same(array(
	'type' => 'DATETIME',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process(\DateTime::class, NULL, FALSE, array(), Database::MYSQL)));

Assert::same(array(
	'type' => 'DATETIME',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process(\DateTimeInterface::class, NULL, FALSE, array(), Database::MYSQL)));
