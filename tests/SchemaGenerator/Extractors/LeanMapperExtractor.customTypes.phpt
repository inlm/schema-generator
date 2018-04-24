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
	), Database::MYSQL);
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same(array(
		'author' => array(
			'name' => 'author',
			'columns' => array(
				'id' => array(
					'name' => 'id',
					'type' => 'INT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'name' => array(
					'name' => 'name',
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'age' => array(
					'name' => 'age',
					'type' => 'INT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'photo' => array(
					'name' => 'photo',
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),
			),

			'indexes' => array(
				'' => array(
					'name' => NULL,
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
			'name' => 'book',
			'columns' => array(
				'id' => array(
					'name' => 'id',
					'type' => 'INT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'name' => array(
					'name' => 'name',
					'type' => 'TEXT',
				),

				'price' => array(
					'name' => 'price',
					'type' => 'DOUBLE',
				),

				'image' => array(
					'name' => 'image',
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'pubdate' => array(
					'name' => 'pubdate',
					'type' => 'DATETIME',
				),

				'available' => array(
					'name' => 'available',
					'type' => 'TINYINT',
					'parameters' => array(1),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),
			),

			'indexes' => array(
				'' => array(
					'name' => NULL,
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
