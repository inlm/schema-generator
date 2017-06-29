<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Test/Diff.php';
require __DIR__ . '/Test/DummyAdapter.php';
require __DIR__ . '/Test/DummyExtractor.php';
require __DIR__ . '/Test/Schema.php';

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
