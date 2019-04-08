<?php

	namespace Test;

	use CzProject\SqlSchema;


	class Schema
	{
		public static function create()
		{
			$schema = new SqlSchema\Schema;

			$schema->addTable(self::createAuthorTable());
			$schema->addTable(self::createBookTable());
			$schema->addTable(self::createBookTagTable());
			$schema->addTable(self::createTagTable());

			return $schema;
		}


		public static function createArray()
		{
			return array(
				'author' => array(
					'columns' => array(
						'id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
							'autoIncrement' => TRUE,
						),

						'name' => array(
							'type' => 'TEXT',
						),

						'web' => array(
							'type' => 'TEXT',
							'nullable' => TRUE,
							'comment' => 'Absolute URL',
						),
					),
					'indexes' => array(
						NULL => array(
							'type' => 'PRIMARY',
							'columns' => array(
								array(
									'name' => 'id',
								),
							),
						),
					),
					'options' => array(
						'CHARSET' => 'UTF-8',
					),
				),

				'book' => array(
					'columns' => array(
						'id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
							'autoIncrement' => TRUE,
						),

						'author_id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
						),

						'reviewer_id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
							'nullable' => TRUE,
						),

						'pubdate' => array(
							'type' => 'DATETIME',
						),

						'name' => array(
							'type' => 'TEXT',
						),

						'description' => array(
							'type' => 'TEXT',
							'nullable' => TRUE,
						),

						'website' => array(
							'type' => 'TEXT',
							'nullable' => TRUE,
						),

						'available' => array(
							'type' => 'TINYINT',
							'parameters' => array(1),
							'options' => array('UNSIGNED' => NULL),
							'defaultValue' => 1,
						),

						'price' => array(
							'type' => 'DOUBLE',
							'nullable' => TRUE,
						),
					),
					'indexes' => array(
						NULL => array(
							'type' => 'PRIMARY',
							'columns' => array(
								array(
									'name' => 'id',
								),
							),
						),
					),
					'foreignKeys' => array(
						'book_fk_author_id' => array(
							'columns' => array('author_id'),
							'targetTable' => 'author',
							'targetColumns' => array('id'),
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						),

						'book_fk_reviewer_id' => array(
							'columns' => array('reviewer_id'),
							'targetTable' => 'author',
							'targetColumns' => array('id'),
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						),
					),
				),

				'book_tag' => array(
					'columns' => array(
						'book_id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
						),

						'tag_id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
						),
					),
					'indexes' => array(
						NULL => array(
							'type' => 'PRIMARY',
							'columns' => array(
								array(
									'name' => 'book_id',
								),

								array(
									'name' => 'tag_id',
								),
							),
						),
						'tag_id' => array(
							'type' => 'INDEX',
							'columns' => array(
								array(
									'name' => 'tag_id',
								),
							),
						),
					),
					'foreignKeys' => array(
						'book_tag_fk_book_id' => array(
							'columns' => array('book_id'),
							'targetTable' => 'book',
							'targetColumns' => array('id'),
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						),

						'book_tag_fk_tag_id' => array(
							'columns' => array('tag_id'),
							'targetTable' => 'tag',
							'targetColumns' => array('id'),
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						),
					),
				),

				'tag' => array(
					'comment' => 'Tags for books.',
					'columns' => array(
						'id' => array(
							'type' => 'INT',
							'options' => array(SqlSchema\Column::OPTION_UNSIGNED => NULL),
							'autoIncrement' => TRUE,
						),

						'name' => array(
							'type' => 'TEXT',
							'parameters' => array(20),
						),
					),
					'indexes' => array(
						NULL => array(
							'type' => 'PRIMARY',
							'columns' => array(
								array(
									'name' => 'id',
								),
							),
						),
					),
				),
			);
		}


		private static function createAuthorTable()
		{
			$table = new SqlSchema\Table('author');

			$table->addColumn('id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED))
				->setAutoIncrement(TRUE);

			$table->addColumn('name', 'TEXT');

			$table->addColumn('web', 'TEXT')
				->setNullable(TRUE)
				->setComment('Absolute URL');

			$table->addIndex(NULL, array('id'), SqlSchema\Index::TYPE_PRIMARY);

			$table->setOption('CHARSET', 'UTF-8');

			return $table;
		}


		private static function createBookTable()
		{
			$table = new SqlSchema\Table('book');

			$table->addColumn('id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED))
				->setAutoIncrement(TRUE);

			$table->addColumn('author_id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED));

			$table->addColumn('reviewer_id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED))
				->setNullable(TRUE);

			$table->addColumn('pubdate', 'DATETIME');

			$table->addColumn('name', 'TEXT');

			$table->addColumn('description', 'TEXT')
				->setNullable(TRUE);

			$table->addColumn('website', 'TEXT')
				->setNullable(TRUE);

			$table->addColumn('available', 'TINYINT', array(1), array('UNSIGNED' => NULL))
				->setDefaultValue(1);

			$table->addColumn('price', 'DOUBLE')
				->setNullable(TRUE);

			$table->addIndex(NULL, array('id'), SqlSchema\Index::TYPE_PRIMARY);

			$table->addForeignKey('book_fk_author_id', array('author_id'), 'author', array('id'));

			$table->addForeignKey('book_fk_reviewer_id', array('reviewer_id'), 'author', array('id'));

			return $table;
		}


		private static function createBookTagTable()
		{
			$table = new SqlSchema\Table('book_tag');

			$table->addColumn('book_id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED));
			$table->addColumn('tag_id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED));

			$table->addIndex(NULL, array('book_id', 'tag_id'), SqlSchema\Index::TYPE_PRIMARY);
			$table->addIndex('tag_id', array('tag_id'), SqlSchema\Index::TYPE_INDEX);
			$table->addForeignKey('book_tag_fk_book_id', array('book_id'), 'book', array('id'));
			$table->addForeignKey('book_tag_fk_tag_id', array('tag_id'), 'tag', array('id'));

			return $table;
		}


		private static function createTagTable()
		{
			$table = new SqlSchema\Table('tag');
			$table->setComment('Tags for books.');

			$table->addColumn('id', 'INT', NULL, array(SqlSchema\Column::OPTION_UNSIGNED))
				->setAutoIncrement(TRUE);

			$table->addColumn('name', 'TEXT', array(20));

			$table->addIndex(NULL, array('id'), SqlSchema\Index::TYPE_PRIMARY);

			return $table;
		}
	}
