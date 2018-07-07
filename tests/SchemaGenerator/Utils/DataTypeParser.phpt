<?php

use Inlm\SchemaGenerator\Utils\DataTypeParser;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	Assert::exception(function () {
		$factory = new DataTypeParser;
	}, 'Inlm\SchemaGenerator\StaticClassException', 'This is static class.');
});


test(function () {
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => NULL,
		'options' => array(),
	), typeToArray(DataTypeParser::parse('TEXT')));


	// alternative
	Assert::same(array(
		'type' => 'TEXT',
		'parameters' => NULL,
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
		'parameters' => NULL,
		'options' => array(
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL INT')));


	// alternative
	Assert::same(array(
		'type' => 'INT',
		'parameters' => NULL,
		'options' => array(
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL INT', DataTypeParser::SYNTAX_ALTERNATIVE)));
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

	Assert::same(array(
		'type' => NULL,
		'parameters' => NULL,
		'options' => array(
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL')));


	// alternative
	Assert::same(array(
		'type' => NULL,
		'parameters' => array(20, 2),
		'options' => array(
			'UNSIGNED' => NULL,
		),
	), typeToArray(DataTypeParser::parse(':20,2 UNSIGNED', DataTypeParser::SYNTAX_ALTERNATIVE)));

	Assert::same(array(
		'type' => NULL,
		'parameters' => NULL,
		'options' => array(
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		),
	), typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same(array(
		'type' => NULL,
		'parameters' => array('(20', 2),
		'options' => array(
			'UNSIGNED' => NULL,
		),
	), typeToArray(DataTypeParser::parse('((20,2) UNSIGNED')));
});


test(function () {
	Assert::same(array(
		'type' => 'ENUM',
		'parameters' => array('ms', 'zs', 'ss'),
		'options' => array(),
	), typeToArray(DataTypeParser::parse("enum(ms,\"zs\",'ss')")));


	// alternative
	Assert::same(array(
		'type' => 'ENUM',
		'parameters' => array('ms', 'zs', 'ss'),
		'options' => array(),
	), typeToArray(DataTypeParser::parse("enum:ms,\"zs\",'ss'", DataTypeParser::SYNTAX_ALTERNATIVE)));
});
