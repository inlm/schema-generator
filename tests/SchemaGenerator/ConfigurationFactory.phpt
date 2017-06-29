<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\ConfigurationFactory;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	Assert::exception(function () {
		$factory = new ConfigurationFactory;
	}, 'Inlm\SchemaGenerator\StaticClassException', 'This is static class.');
});


test(function () {
	$configuration = ConfigurationFactory::fromArray(array());

	Assert::same(array(), $configuration->getOptions());
	Assert::same(array(), $configuration->getSchema()->getTables());
});


test(function () {
	$configuration = ConfigurationFactory::fromArray(array(
		'options' => array(
			'ENGINE' => 'InnoDB',
		),
		'schema' => Test\Schema::createArray(),
	));

	Assert::same(array(
		'ENGINE' => 'InnoDB',
	), $configuration->getOptions());

	$config = ConfigurationSerializer::serialize($configuration);

	Assert::same(Test\Schema::createArray(), $config['schema']);
});
