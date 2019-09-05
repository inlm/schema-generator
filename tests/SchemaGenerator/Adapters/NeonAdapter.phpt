<?php

use Inlm\SchemaGenerator\Adapters;
use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Nette\Neon\Neon;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

define('TEMP_DIR', prepareTempDir());
$content = "# DON'T EDIT THIS FILE!\n\n" . Neon::encode([
	'schema' => Test\Schema::createArray(),
], Neon::BLOCK);
$content = rtrim($content) . "\n";

test(function () {
	$adapter = new Adapters\NeonAdapter(TEMP_DIR . '/empty.neon');
	$configuration = $adapter->load();

	Assert::same([], $configuration->getOptions());
	Assert::same([], $configuration->getSchema()->getTables());
});


test(function () use ($content) {
	$file = TEMP_DIR . '/load.neon';
	file_put_contents($file, $content);
	$adapter = new Adapters\NeonAdapter($file);
	$configuration = $adapter->load();

	Assert::same([], $configuration->getOptions());
	Assert::same([
		'schema' => Test\Schema::createArray(),
	], ConfigurationSerializer::serialize($configuration));
});


test(function () use ($content) {
	$file = TEMP_DIR . '/save.neon';
	$adapter = new Adapters\NeonAdapter($file);
	$adapter->save(new Configuration(Test\Schema::create()));

	Assert::same($content, file_get_contents($file));
});
