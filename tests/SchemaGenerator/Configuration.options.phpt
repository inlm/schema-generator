<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\Configuration;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$schema = new SqlSchema\Schema;
	$configuration = new Configuration($schema);

	Assert::same($schema, $configuration->getSchema());
	Assert::same(array(), $configuration->getOptions());
});


test(function () {
	$configuration = new Configuration(new SqlSchema\Schema);
	$configuration->setOptions(array(
		'CHARSET' => 'UTF-8',
		'ENGINE' => 'InnoDB',
	));

	Assert::same(array(
		'CHARSET' => 'UTF-8',
		'ENGINE' => 'InnoDB',
	), $configuration->getOptions());
});
