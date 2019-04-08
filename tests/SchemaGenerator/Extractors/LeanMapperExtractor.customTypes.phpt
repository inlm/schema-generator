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

	$schema = $extractor->generateSchema(array(), array(
		'test\leanmapperextractor\customtypes\image' => new DataType('varchar', array(100)),
		'money' => new DataType('DECIMAL', array(15, 4)),
	), Database::MYSQL);
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same(array(
		'author' => array(
			'columns' => array(
				'id' => array(
					'type' => 'INT',
					'parameters' => array(10),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'name' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'age' => array(
					'type' => 'INT',
					'parameters' => array(10),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'photo' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),
			),

			'indexes' => array(
				'' => array(
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => array(
						array(
							'name' => 'id',
						),
					),
				)
			)
		),

		'book' => array(
			'columns' => array(
				'id' => array(
					'type' => 'INT',
					'parameters' => array(10),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'name' => array(
					'type' => 'TEXT',
				),

				'price' => array(
					'type' => 'DOUBLE',
				),

				'sellPrice' => array(
					'type' => 'DECIMAL',
					'parameters' => array(15, 4),
					'nullable' => TRUE,
				),

				'image' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'pubdate' => array(
					'type' => 'DATETIME',
				),

				'available' => array(
					'type' => 'TINYINT',
					'parameters' => array(1),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),
			),

			'indexes' => array(
				'' => array(
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => array(
						array(
							'name' => 'id',
						),
					),
				)
			)
		),
	), $generated);
});
