<?php

	declare(strict_types=1);

	namespace Test;

	use CzProject\SqlSchema;


	class Schema
	{
		/**
		 * @return SqlSchema\Schema
		 */
		public static function create()
		{
			$schema = new SqlSchema\Schema;

			$schema->addTable(self::createAuthorTable());
			$schema->addTable(self::createBookTable());
			$schema->addTable(self::createBookTagTable());
			$schema->addTable(self::createTagTable());

			return $schema;
		}


		/**
		 * @return array<string, mixed>
		 */
		public static function createArray()
		{
			return [
				'author' => [
					'columns' => [
						'id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
							'autoIncrement' => TRUE,
						],

						'name' => [
							'type' => 'TEXT',
						],

						'web' => [
							'type' => 'TEXT',
							'nullable' => TRUE,
							'comment' => 'Absolute URL',
						],
					],
					'indexes' => [
						NULL => [
							'type' => 'PRIMARY',
							'columns' => [
								[
									'name' => 'id',
								],
							],
						],
					],
					'options' => [
						'CHARSET' => 'UTF-8',
					],
				],

				'book' => [
					'columns' => [
						'id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
							'autoIncrement' => TRUE,
						],

						'author_id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
						],

						'reviewer_id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
							'nullable' => TRUE,
						],

						'pubdate' => [
							'type' => 'DATETIME',
						],

						'name' => [
							'type' => 'TEXT',
						],

						'description' => [
							'type' => 'TEXT',
							'nullable' => TRUE,
						],

						'website' => [
							'type' => 'TEXT',
							'nullable' => TRUE,
						],

						'available' => [
							'type' => 'TINYINT',
							'parameters' => [1],
							'options' => ['UNSIGNED' => NULL],
							'defaultValue' => 1,
						],

						'price' => [
							'type' => 'DOUBLE',
							'nullable' => TRUE,
						],
					],
					'indexes' => [
						NULL => [
							'type' => 'PRIMARY',
							'columns' => [
								[
									'name' => 'id',
								],
							],
						],
					],
					'foreignKeys' => [
						'book_fk_author_id' => [
							'columns' => ['author_id'],
							'targetTable' => 'author',
							'targetColumns' => ['id'],
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						],

						'book_fk_reviewer_id' => [
							'columns' => ['reviewer_id'],
							'targetTable' => 'author',
							'targetColumns' => ['id'],
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						],
					],
				],

				'book_tag' => [
					'columns' => [
						'book_id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
						],

						'tag_id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
						],
					],
					'indexes' => [
						NULL => [
							'type' => 'PRIMARY',
							'columns' => [
								[
									'name' => 'book_id',
								],

								[
									'name' => 'tag_id',
								],
							],
						],
						'tag_id' => [
							'type' => 'INDEX',
							'columns' => [
								[
									'name' => 'tag_id',
								],
							],
						],
					],
					'foreignKeys' => [
						'book_tag_fk_book_id' => [
							'columns' => ['book_id'],
							'targetTable' => 'book',
							'targetColumns' => ['id'],
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						],

						'book_tag_fk_tag_id' => [
							'columns' => ['tag_id'],
							'targetTable' => 'tag',
							'targetColumns' => ['id'],
							'onUpdateAction' => 'RESTRICT',
							'onDeleteAction' => 'RESTRICT',
						],
					],
				],

				'tag' => [
					'comment' => 'Tags for books.',
					'columns' => [
						'id' => [
							'type' => 'INT',
							'options' => [SqlSchema\Column::OPTION_UNSIGNED => NULL],
							'autoIncrement' => TRUE,
						],

						'name' => [
							'type' => 'TEXT',
							'parameters' => [20],
						],
					],
					'indexes' => [
						NULL => [
							'type' => 'PRIMARY',
							'columns' => [
								[
									'name' => 'id',
								],
							],
						],
					],
				],
			];
		}


		/**
		 * @return SqlSchema\Table
		 */
		private static function createAuthorTable()
		{
			$table = new SqlSchema\Table('author');

			$table->addColumn('id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED])
				->setAutoIncrement(TRUE);

			$table->addColumn('name', 'TEXT');

			$table->addColumn('web', 'TEXT')
				->setNullable(TRUE)
				->setComment('Absolute URL');

			$table->addIndex(NULL, ['id'], SqlSchema\Index::TYPE_PRIMARY);

			$table->setOption('CHARSET', 'UTF-8');

			return $table;
		}


		/**
		 * @return SqlSchema\Table
		 */
		private static function createBookTable()
		{
			$table = new SqlSchema\Table('book');

			$table->addColumn('id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED])
				->setAutoIncrement(TRUE);

			$table->addColumn('author_id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED]);

			$table->addColumn('reviewer_id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED])
				->setNullable(TRUE);

			$table->addColumn('pubdate', 'DATETIME');

			$table->addColumn('name', 'TEXT');

			$table->addColumn('description', 'TEXT')
				->setNullable(TRUE);

			$table->addColumn('website', 'TEXT')
				->setNullable(TRUE);

			$table->addColumn('available', 'TINYINT', [1], ['UNSIGNED' => NULL])
				->setDefaultValue(1);

			$table->addColumn('price', 'DOUBLE')
				->setNullable(TRUE);

			$table->addIndex(NULL, ['id'], SqlSchema\Index::TYPE_PRIMARY);

			$table->addForeignKey('book_fk_author_id', ['author_id'], 'author', ['id']);

			$table->addForeignKey('book_fk_reviewer_id', ['reviewer_id'], 'author', ['id']);

			return $table;
		}


		/**
		 * @return SqlSchema\Table
		 */
		private static function createBookTagTable()
		{
			$table = new SqlSchema\Table('book_tag');

			$table->addColumn('book_id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED]);
			$table->addColumn('tag_id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED]);

			$table->addIndex(NULL, ['book_id', 'tag_id'], SqlSchema\Index::TYPE_PRIMARY);
			$table->addIndex('tag_id', ['tag_id'], SqlSchema\Index::TYPE_INDEX);
			$table->addForeignKey('book_tag_fk_book_id', ['book_id'], 'book', ['id']);
			$table->addForeignKey('book_tag_fk_tag_id', ['tag_id'], 'tag', ['id']);

			return $table;
		}


		/**
		 * @return SqlSchema\Table
		 */
		private static function createTagTable()
		{
			$table = new SqlSchema\Table('tag');
			$table->setComment('Tags for books.');

			$table->addColumn('id', 'INT', NULL, [SqlSchema\Column::OPTION_UNSIGNED])
				->setAutoIncrement(TRUE);

			$table->addColumn('name', 'TEXT', [20]);

			$table->addIndex(NULL, ['id'], SqlSchema\Index::TYPE_PRIMARY);

			return $table;
		}
	}
