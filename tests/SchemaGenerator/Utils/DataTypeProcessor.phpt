<?php

use CzProject\SqlSchema\Column;
use Inlm\SchemaGenerator\Database;
use Inlm\SchemaGenerator\DataType;
use Inlm\SchemaGenerator\Utils\DataTypeProcessor;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


Assert::same([
	'type' => 'MONEY',
	'parameters' => [],
	'options' => [],
], typeToArray(DataTypeProcessor::process('float', new DataType('money'))));


Assert::same([
	'type' => 'DECIMAL',
	'parameters' => [15, 4],
	'options' => [],
], typeToArray(DataTypeProcessor::process('float', new DataType('money'), FALSE, [
	'money' => new DataType('DECIMAL', [15, 4]),
])));


Assert::same([
	'type' => 'DECIMAL',
	'parameters' => [15, 4],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('float', new DataType('money', NULL, [Column::OPTION_UNSIGNED]), FALSE, [
	'money' => new DataType('DECIMAL', [15, 4]),
])));


Assert::same([
	'type' => 'DECIMAL',
	'parameters' => [10, 2],
	'options' => [Column::OPTION_UNSIGNED => NULL],
], typeToArray(DataTypeProcessor::process('float', new DataType('money', [10, 2], [Column::OPTION_UNSIGNED]), FALSE, [
	'money' => new DataType('DECIMAL', [15, 4]),
])));
