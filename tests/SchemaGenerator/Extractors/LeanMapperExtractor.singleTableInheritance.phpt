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
			'name' => 'user',
			'columns' => array(
				'id' => array(
					'name' => 'id',
					'type' => 'INT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
					'autoIncrement' => TRUE,
				),

				'type' => array(
					'name' => 'type',
					'type' => 'TINYINT',
					'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
				),

				'created' => array(
					'name' => 'created',
					'type' => 'DATETIME',
				),

				'updated' => array(
					'name' => 'updated',
					'type' => 'DATETIME',
					'nullable' => TRUE,
				),

				'companyName' => array(
					'name' => 'companyName',
					'type' => 'VARCHAR',
					'parameters' => array(200),
					'nullable' => TRUE,
				),

				'ico' => array(
					'name' => 'ico',
					'type' => 'VARCHAR',
					'parameters' => array(8),
					'nullable' => TRUE,
				),

				'note' => array(
					'name' => 'note',
					'type' => 'VARCHAR',
					'parameters' => array(100),
				),

				'firstName' => array(
					'name' => 'firstName',
					'type' => 'VARCHAR',
					'parameters' => array(100),
					'nullable' => TRUE,
				),

				'lastName' => array(
					'name' => 'lastName',
					'type' => 'VARCHAR',
					'parameters' => array(100),
					'nullable' => TRUE,
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
