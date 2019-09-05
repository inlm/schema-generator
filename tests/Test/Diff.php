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
				->setParameters([200]);
			$oldBook->addIndex(NULL, 'id', SqlSchema\Index::TYPE_PRIMARY);
			$oldBook->addIndex('author_id', 'author_id', SqlSchema\Index::TYPE_INDEX);
			$oldBook->addForeignKey('fk_author', 'author_id', 'author', 'id');

			$oldAuthor = $old->addTable('author');
			$oldAuthor->addColumn('id', 'INT') // nemeni se
				->setAutoIncrement();
			$oldAuthor->addColumn('name') // meni typ
				->setType('VARCHAR')
				->setParameters([50]);
			$oldAuthor->addColumn('tag_id') // smazany
				->setType('INT');
			$oldAuthor->addIndex(NULL, 'id', SqlSchema\Index::TYPE_PRIMARY); // without changes
			$oldAuthor->addIndex('name', 'name', SqlSchema\Index::TYPE_UNIQUE); // updated
			$oldAuthor->addIndex('tag_id', 'tag_id', SqlSchema\Index::TYPE_INDEX); // removed
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
				->setParameters([200]);
			$newBook->addIndex(NULL, 'id', SqlSchema\Index::TYPE_PRIMARY);
			$newBook->addIndex('author_id', 'author_id', SqlSchema\Index::TYPE_INDEX);
			$newBook->addForeignKey('fk_author', 'author_id', 'author', 'id');

			$newAuthor = $new->addTable('author');
			$newAuthor->addColumn('id', 'INT') // nemeni se
				->setAutoIncrement();
			$newAuthor->addColumn('name') // meni typ
				->setType('VARCHAR')
				->setParameters([200]);
			$newAuthor->addColumn('website') // novy sloupec
				->setType('VARCHAR')
				->setParameters([255])
				->setNullable();
			$newAuthor->addIndex(NULL, 'id', SqlSchema\Index::TYPE_PRIMARY); // without changes
			$newAuthor->addIndex('name', 'name', SqlSchema\Index::TYPE_INDEX); // updated
			$newAuthor->addIndex('website', 'website', SqlSchema\Index::TYPE_INDEX); // created
			$newAuthor->addForeignKey('fk_section', 'section_id', 'section', 'id'); // created
			$newAuthor->addForeignKey('fk_updated', 'role_id', 'roles', 'id'); // updated

			$new->addTable('tag');

			return $new;
		}
	}
