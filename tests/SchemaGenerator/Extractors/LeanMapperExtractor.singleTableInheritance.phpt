<?php

use CzProject\SqlSchema;
use Inlm\SchemaGenerator\Configuration;
use Inlm\SchemaGenerator\ConfigurationSerializer;
use Inlm\SchemaGenerator\Extractors\LeanMapperExtractor;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/single-table-inheritance/User.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/single-table-inheritance/UserIndividual.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/single-table-inheritance/UserCompany.php';
require __DIR__ . '/../../Test/LeanMapperExtractor/single-table-inheritance/Mapper.php';


test(function () {
	$extractor = new LeanMapperExtractor(__DIR__ . '/../../Test/LeanMapperExtractor/single-table-inheritance', new Test\LeanMapperExtractor\SingleTableInheritance\Mapper);

	$schema = $extractor->generateSchema();
	$serialized = ConfigurationSerializer::serialize(new Configuration($schema));
	$generated = $serialized['schema'];
	ksort($generated, SORT_STRING);

	Assert::same(array(
		'user' => array(
			'columns' => array(
				'id' => array(
					'type' => 'INT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'type' => array(
					'type' => 'TINYINT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'created' => array(
					'type' => 'DATETIME',
				),

				'updated' => array(
					'type' => 'DATETIME',
					'nullable' => TRUE,
				),

				'companyName' => array(
					'type' => 'VARCHAR',
					'parameters' => array(200),
					'nullable' => TRUE,
				),

				'ico' => array(
					'type' => 'VARCHAR',
					'parameters' => array(8),
					'nullable' => TRUE,
				),

				'note' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'firstName' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
					'nullable' => TRUE,
				),

				'lastName' => array(
					'type' => 'VARCHAR',
					'parameters' => array(100),
					'nullable' => TRUE,
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
