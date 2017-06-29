<?php

use Inlm\SchemaGenerator\Loggers\OutputLogger;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {
	$logger = new OutputLogger;

	ob_start();

	$logger->log('Lorem ipsum');
	$logger->log(' - dolor sit amet');

	$content = ob_get_contents();
	ob_flush();

	Assert::same($content, "Lorem ipsum\n - dolor sit amet\n");
});
