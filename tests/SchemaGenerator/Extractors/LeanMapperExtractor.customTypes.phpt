<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Inlm\SchemaGenerator\Database;
use Inlm\SchemaGenerator\DataType;
use Inlm\SchemaGenerator\Extractors\LeanMapperExtractor;
use Inlm\SchemaGenerator\SchemaGenerator;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/custom-types/Author.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/custom-types/Book.php';


test(function () {
	$extractor = new LeanMapperExtractor(__DIR__ . '/../../Test/LeanMapperExtractor/custom-types', new \LeanMapper\DefaultMapper);

	$schema = $extractor->generateSchema([], [
		'test\leanmapperextractor\customtypes\image' => new DataType('varchar', [100]),
		'money' => new DataType('DECIMAL', [15, 4]),
	], Database::MYSQL);
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same([
		'author' => [
			'columns' => [
				'id' => [
					'type' => 'INT',
					'parameters' => [10],
					'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
					'autoIncrement' => TRUE,
				],

				'name' => [
					'type' => 'VARCHAR',
					'parameters' => [100],
				],

				'age' => [
					'type' => 'INT',
					'parameters' => [10],
					'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
				],

				'photo' => [
					'type' => 'VARCHAR',
					'parameters' => [100],
				],
			],

			'indexes' => [
				'' => [
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => [
						[
							'name' => 'id',
						],
					],
				]
			]
		],

		'book' => [
			'columns' => [
				'id' => [
					'type' => 'INT',
					'parameters' => [10],
					'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
					'autoIncrement' => TRUE,
				],

				'name' => [
					'type' => 'TEXT',
				],

				'price' => [
					'type' => 'DOUBLE',
				],

				'sellPrice' => [
					'type' => 'DECIMAL',
					'parameters' => [15, 4],
					'nullable' => TRUE,
				],

				'image' => [
					'type' => 'VARCHAR',
					'parameters' => [100],
				],

				'pubdate' => [
					'type' => 'DATETIME',
				],

				'available' => [
					'type' => 'TINYINT',
					'parameters' => [1],
					'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
				],
			],

			'indexes' => [
				'' => [
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => [
						[
							'name' => 'id',
						],
					],
				]
			]
		],
	], $generated);
});
