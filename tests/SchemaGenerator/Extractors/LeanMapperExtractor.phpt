<?php

use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Inlm\SchemaGenerator\Extractors\LeanMapperExtractor;
use Nette\Neon\Neon;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/basic/Person.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/basic/Author.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/basic/Book.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/basic/Tag.php';


test(function () {
	$extractor = new LeanMapperExtractor(__DIR__ . '/../../Test/LeanMapperExtractor/basic', new \LeanMapper\DefaultMapper);

	$schema = $extractor->generateSchema();
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same(Test\Schema::createArray(), $generated);
});
