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
require __DIR__ . '/../../Test/LeanMapperExtractor/primary-key/Mapper.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/primary-key/Book.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/primary-key/BookMeta.php';


test(function () {
	$extractor = new LeanMapperExtractor(__DIR__ . '/../../Test/LeanMapperExtractor/primary-key', new \Test\LeanMapperExtractor\PrimaryKey\Mapper);

	$schema = $extractor->generateSchema(array(), array(), Database::MYSQL);
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same(array(
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

		'bookmeta' => array(
			'columns' => array(
				'book_id' => array(
					'type' => 'INT',
					'parameters' => array(10),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'year' => array(
					'type' => 'INT',
					'parameters' => array(11),
				),
			),

			'indexes' => array(
				'' => array(
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => array(
						array(
							'name' => 'book_id',
						),
					),
				)
			),

			'foreignKeys' => array(
				'bookmeta_fk_book_id' => array(
					'columns' => array('book_id'),
					'targetTable' => 'book',
					'targetColumns' => array('id'),
					'onUpdateAction' => 'RESTRICT',
					'onDeleteAction' => 'RESTRICT',
				),
			),
		),

		'bookmeta2' => array(
			'columns' => array(
				'book_id' => array(
					'type' => 'INT',
					'parameters' => array(10),
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'rating' => array(
					'type' => 'INT',
					'parameters' => array(11),
				),
			),

			'indexes' => array(
				'' => array(
					'type' => SqlSchema\Index::TYPE_PRIMARY,
					'columns' => array(
						array(
							'name' => 'book_id',
						),
					),
				)
			),

			'foreignKeys' => array(
				'bookmeta2_fk_book_id' => array(
					'columns' => array('book_id'),
					'targetTable' => 'book',
					'targetColumns' => array('id'),
					'onUpdateAction' => 'RESTRICT',
					'onDeleteAction' => 'RESTRICT',
				),
			),
		),
	), $generated);
});
