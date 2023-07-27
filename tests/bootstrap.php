<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Test/Diff.php';
require __DIR__ . '/Test/DummyAdapter.php';
require __DIR__ . '/Test/DummyExtractor.php';
require __DIR__ . '/Test/Schema.php';
require __DIR__ . '/Test/TestGenerator.php';

Tester\Environment::setup();


/**
 * @return void
 */
function test(callable $cb)
{
	$cb();
}


/**
 * @return string
 */
function prepareTempDir()
{
	$dir = __DIR__ . '/tmp/' . getmypid();
	Tester\Helpers::purge($dir);
	return $dir;
}


/**
 * @return array{
 *   type: string|NULL,
 *   parameters: scalar[]|NULL,
 *   options: array<string, scalar|NULL>
 * }
 */
function typeToArray(Inlm\SchemaGenerator\DataType $type)
{
	return [
		'type' => $type->getType(),
		'parameters' => $type->getParameters(),
		'options' => $type->getOptions(),
	];
}
