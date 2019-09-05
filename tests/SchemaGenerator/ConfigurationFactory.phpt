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
	$configuration = ConfigurationFactory::fromArray([]);

	Assert::same([], $configuration->getOptions());
	Assert::same([], $configuration->getSchema()->getTables());
});


test(function () {
	$configuration = ConfigurationFactory::fromArray([
		'options' => [
			'ENGINE' => 'InnoDB',
		],
		'schema' => Test\Schema::createArray(),
	]);

	Assert::same([
		'ENGINE' => 'InnoDB',
	], $configuration->getOptions());

	$config = ConfigurationSerializer::serialize($configuration);

	Assert::same(Test\Schema::createArray(), $config['schema']);
});
