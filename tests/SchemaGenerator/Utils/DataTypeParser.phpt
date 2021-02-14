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
	Assert::same([
		'type' => 'TEXT',
		'parameters' => NULL,
		'options' => [],
	], typeToArray(DataTypeParser::parse('TEXT')));


	// alternative
	Assert::same([
		'type' => 'TEXT',
		'parameters' => NULL,
		'options' => [],
	], typeToArray(DataTypeParser::parse('TEXT', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same([
		'type' => 'TEXT',
		'parameters' => [20],
		'options' => [],
	], typeToArray(DataTypeParser::parse('TEXT(20)')));

	Assert::same([
		'type' => 'DECIMAL',
		'parameters' => [15, 4],
		'options' => [],
	], typeToArray(DataTypeParser::parse('decimal(15, 4)')));


	// alternative
	Assert::same([
		'type' => 'TEXT',
		'parameters' => [20],
		'options' => [],
	], typeToArray(DataTypeParser::parse('TEXT:20', DataTypeParser::SYNTAX_ALTERNATIVE)));

	Assert::same([
		'type' => 'DECIMAL',
		'parameters' => [15, 4],
		'options' => [],
	], typeToArray(DataTypeParser::parse('decimal:15,4', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same([
		'type' => 'INT',
		'parameters' => NULL,
		'options' => [
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		],
	], typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL INT')));


	// alternative
	Assert::same([
		'type' => 'INT',
		'parameters' => NULL,
		'options' => [
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		],
	], typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL INT', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same([
		'type' => 'DECIMAL',
		'parameters' => [20, 2],
		'options' => [
			'UNSIGNED' => NULL,
			'SUPER_OPTION' => NULL,
		],
	], typeToArray(DataTypeParser::parse('DECIMAL(20,2) UNSIGNED SUPER_OPTION')));


	// alternative
	Assert::same([
		'type' => 'DECIMAL',
		'parameters' => [20, 2],
		'options' => [
			'UNSIGNED' => NULL,
			'SUPER_OPTION' => NULL,
		],
	], typeToArray(DataTypeParser::parse('DECIMAL:20,2 UNSIGNED SUPER_OPTION', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same([
		'type' => NULL,
		'parameters' => [20, 2],
		'options' => [
			'UNSIGNED' => NULL,
		],
	], typeToArray(DataTypeParser::parse('(20,2) UNSIGNED')));

	Assert::same([
		'type' => NULL,
		'parameters' => NULL,
		'options' => [
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		],
	], typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL')));


	// alternative
	Assert::same([
		'type' => NULL,
		'parameters' => [20, 2],
		'options' => [
			'UNSIGNED' => NULL,
		],
	], typeToArray(DataTypeParser::parse(':20,2 UNSIGNED', DataTypeParser::SYNTAX_ALTERNATIVE)));

	Assert::same([
		'type' => NULL,
		'parameters' => [20],
		'options' => [],
	], typeToArray(DataTypeParser::parse(':20', DataTypeParser::SYNTAX_ALTERNATIVE)));

	Assert::same([
		'type' => NULL,
		'parameters' => NULL,
		'options' => [
			'UNSIGNED' => NULL,
			'ZEROFILL' => NULL,
		],
	], typeToArray(DataTypeParser::parse('UNSIGNED ZEROFILL', DataTypeParser::SYNTAX_ALTERNATIVE)));
});


test(function () {
	Assert::same([
		'type' => NULL,
		'parameters' => ['(20', 2],
		'options' => [
			'UNSIGNED' => NULL,
		],
	], typeToArray(DataTypeParser::parse('((20,2) UNSIGNED')));
});


test(function () {
	Assert::same([
		'type' => 'ENUM',
		'parameters' => ['ms', 'zs', 'ss'],
		'options' => [],
	], typeToArray(DataTypeParser::parse("enum(ms,\"zs\",'ss')")));


	// alternative
	Assert::same([
		'type' => 'ENUM',
		'parameters' => ['ms', 'zs', 'ss'],
		'options' => [],
	], typeToArray(DataTypeParser::parse("enum:ms,\"zs\",'ss'", DataTypeParser::SYNTAX_ALTERNATIVE)));
});
