<?php

use Inlm\SchemaGenerator\Utils\DataTypeParser;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


function typeToArray(Inlm\SchemaGenerator\DataType $type)
{
	return array(
		'type' => $type->getType(),
		'parameters' => $type->getParameters(),
		'options' => $type->getOptions(),
	);
}


test(function () {
	Assert::exception(function () {
		$factory = new DataTypeParser;
	}, 'Inlm\SchemaGenerator\StaticClassException', 'This is static class.');
});


test(function () {
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => array(),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('TEXT')));


	// alternative
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => array(),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('TEXT', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => array(20),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('TEXT(20)')));

	Assert::same(array(
		'type' => 'DECIMAL',
		'parameters' => array(15, 4),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('decimal(15, 4)')));


	// alternative
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => array(20),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('TEXT:20', DataTypeParser::SYNTAX_ALTERNATIVE)));

	Assert::same(array(
		'type' => 'DECIMAL',
		'parameters' => array(15, 4),
		'options' => array(),
	), typeToArray(DataTypeParser::parse('decimal:15,4', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same(array(
		'type' => 'INT',
		'parameters' => array(),
		'options' => array(
			'UNSIGNED' => NULL,
			'BINARY' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED BINARY ZEROFILL INT')));


	// alternative
	Assert::same(array(
		'type' => 'INT',
		'parameters' => array(),
		'options' => array(
			'UNSIGNED' => NULL,
			'BINARY' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED BINARY ZEROFILL INT', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same(array(
		'type' => 'DECIMAL',
		'parameters' => array(20, 2),
		'options' => array(
			'UNSIGNED' => NULL,
			'SUPER_OPTION' => NULL,
		),
	), typeToArray(DataTypeParser::parse('DECIMAL(20,2) UNSIGNED SUPER_OPTION')));


	// alternative
	Assert::same(array(
		'type' => 'DECIMAL',
		'parameters' => array(20, 2),
		'options' => array(
			'UNSIGNED' => NULL,
			'SUPER_OPTION' => NULL,
		),
	), typeToArray(DataTypeParser::parse('DECIMAL:20,2 UNSIGNED SUPER_OPTION', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same(array(
		'type' => NULL,
		'parameters' => array(20, 2),
		'options' => array(
			'UNSIGNED' => NULL,
		),
	), typeToArray(DataTypeParser::parse('(20,2) UNSIGNED')));


	// alternative
	Assert::same(array(
		'type' => NULL,
		'parameters' => array(20, 2),
		'options' => array(
			'UNSIGNED' => NULL,
		),
	), typeToArray(DataTypeParser::parse(':20,2 UNSIGNED', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::exception(function () {
		DataTypeParser::parse('((20,2) UNSIGNED');
	}, 'Inlm\SchemaGenerator\InvalidArgumentException', "Value must be integer, '(20' given.");
});
