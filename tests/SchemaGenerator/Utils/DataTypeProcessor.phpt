<?php

use CzProject\SqlSchema\Column;
use Inlm\SchemaGenerator\Database;
use Inlm\SchemaGenerator\DataType;
use Inlm\SchemaGenerator\Utils\DataTypeProcessor;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


Assert::same(array(
	'type' => 'MONEY',
	'parameters' => array(),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('float', new DataType('money'))));


Assert::same(array(
	'type' => 'DECIMAL',
	'parameters' => array(15, 4),
	'options' => array(),
), typeToArray(DataTypeProcessor::process('float', new DataType('money'), FALSE, array(
	'money' => new DataType('DECIMAL', array(15, 4)),
))));


Assert::same(array(
	'type' => 'DECIMAL',
	'parameters' => array(15, 4),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('float', new DataType('money', NULL, array(Column::OPTION_UNSIGNED)), FALSE, array(
	'money' => new DataType('DECIMAL', array(15, 4)),
))));


Assert::same(array(
	'type' => 'DECIMAL',
	'parameters' => array(10, 2),
	'options' => array(Column::OPTION_UNSIGNED => NULL),
), typeToArray(DataTypeProcessor::process('float', new DataType('money', array(10, 2), array(Column::OPTION_UNSIGNED)), FALSE, array(
	'money' => new DataType('DECIMAL', array(15, 4)),
))));
