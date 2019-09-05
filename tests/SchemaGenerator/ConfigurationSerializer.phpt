<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	Assert::exception(function () {
		$serializer = new ConfigurationSerializer;
	}, 'Inlm\SchemaGenerator\StaticClassException', 'This is static class.');
});


test(function () {
	$schema = new SqlSchema\Schema;
	$configuration = new Configuration($schema);

	Assert::same([], ConfigurationSerializer::serialize($configuration));
});


test(function () {
	$schema = new SqlSchema\Schema;
	$configuration = new Configuration($schema);
	$configuration->setOptions([
		'STORAGE' => '',
		'ENGINE' => 'InnoDB',
		'CHARSET' => 'UTF-8',
	]);

	Assert::same([
		'options' => [
			'CHARSET' => 'UTF-8',
			'ENGINE' => 'InnoDB',
			'STORAGE' => '',
		],
	], ConfigurationSerializer::serialize($configuration));
});


test(function () {
	$configuration = new Configuration(Test\Schema::create());
	$configuration->setOptions([
		'ENGINE' => 'InnoDB',
		'CHARSET' => 'UTF-8',
	]);

	Assert::same([
		'options' => [
			'CHARSET' => 'UTF-8',
			'ENGINE' => 'InnoDB',
		],

		'schema' => Test\Schema::createArray(),
	], ConfigurationSerializer::serialize($configuration));
});
