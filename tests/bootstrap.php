<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Test/Diff.php';
require __DIR__ . '/Test/DummyAdapter.php';
require __DIR__ . '/Test/DummyExtractor.php';
require __DIR__ . '/Test/Schema.php';
require __DIR__ . '/Test/TestGenerator.php';

Tester\Environment::setup();


function test($cb)
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


function typeToArray(Inlm\SchemaGenerator\DataType $type)
{
	return array(
		'type' => $type->getType(),
		'parameters' => $type->getParameters(),
		'options' => $type->getOptions(),
	);
}
