<?php

use Inlm\SchemaGenerator\Loggers\MemoryLogger;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$logger = new MemoryLogger;
	$logger->log('Lorem ipsum');
	$logger->log(' - dolor sit amet');

	Assert::same($logger->getLog(), "Lorem ipsum\n - dolor sit amet\n");
});
