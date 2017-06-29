<?php

	namespace Test;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\DiffGenerator;


	// book - table without changes
	// author - table with changes
	// tag - empty table - without changes
	class Diff
	{
		public static function create()
		{
			// diffs
			return new DiffGenerator(self::createOldSchema(), self::createNewSchema());
		}


		public static function createOldSchema()
		{
			$old = new SqlSchema\Schema;

			// old tables
			$oldBook = $old->addTable('book');
			$oldBook->setComment('All books.');
			$oldBook->addColumn('id', 'INT')
				->setAutoIncrement();
			$oldBook->addColumn('author_id', 'INT')
				->setNullable();
			$oldBook->addColumn('name')
				->setType('VARCHAR')
				->setParameters(array(200));
			$oldBook->addIndex(NULL, SqlSchema\Index::TYPE_PRIMARY, 'id');
			$oldBook->addIndex('author_id', SqlSchema\Index::TYPE_INDEX, 'author_id');
			$oldBook->addForeignKey('fk_author', 'author_id', 'author', 'id');

			$oldAuthor = $old->addTable('author');
			$oldAuthor->addColumn('id', 'INT') // nemeni se
				->setAutoIncrement();
			$oldAuthor->addColumn('name') // meni typ
				->setType('VARCHAR')
				->setParameters(array(50));
			$oldAuthor->addColumn('tag_id') // smazany
				->setType('INT');
			$oldAuthor->addIndex(NULL, SqlSchema\Index::TYPE_PRIMARY, 'id'); // without changes
			$oldAuthor->addIndex('name', SqlSchema\Index::TYPE_UNIQUE, 'name'); // updated
			$oldAuthor->addIndex('tag_id', SqlSchema\Index::TYPE_INDEX, 'tag_id'); // removed
			$oldAuthor->addForeignKey('fk_updated', 'role_id', 'role', 'id'); // updated
			$oldAuthor->addForeignKey('fk_tag', 'tag_id', 'tag', 'id'); // removed

			$old->addTable('tag');

			return $old;
		}


		public static function createNewSchema()
		{
			$new = new SqlSchema\Schema;

			// new tables
			$newBook = $new->addTable('book');
			$newBook->setComment('All books.');
			$newBook->addColumn('id', 'INT')
				->setAutoIncrement();
			$newBook->addColumn('author_id', 'INT')
				->setNullable();
			$newBook->addColumn('name')
				->setType('VARCHAR')
				->setParameters(array(200));
			$newBook->addIndex(NULL, SqlSchema\Index::TYPE_PRIMARY, 'id');
			$newBook->addIndex('author_id', SqlSchema\Index::TYPE_INDEX, 'author_id');
			$newBook->addForeignKey('fk_author', 'author_id', 'author', 'id');

			$newAuthor = $new->addTable('author');
			$newAuthor->addColumn('id', 'INT') // nemeni se
				->setAutoIncrement();
			$newAuthor->addColumn('name') // meni typ
				->setType('VARCHAR')
				->setParameters(array(200));
			$newAuthor->addColumn('website') // novy sloupec
				->setType('VARCHAR')
				->setParameters(array(255))
				->setNullable();
			$newAuthor->addIndex(NULL, SqlSchema\Index::TYPE_PRIMARY, 'id'); // without changes
			$newAuthor->addIndex('name', SqlSchema\Index::TYPE_INDEX, 'name'); // updated
			$newAuthor->addIndex('website', SqlSchema\Index::TYPE_INDEX, 'website'); // created
			$newAuthor->addForeignKey('fk_section', 'section_id', 'section', 'id'); // created
			$newAuthor->addForeignKey('fk_updated', 'role_id', 'roles', 'id'); // updated

			$new->addTable('tag');

			return $new;
		}
	}
