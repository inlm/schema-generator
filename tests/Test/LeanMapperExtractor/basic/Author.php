<?php

	namespace Test\LeanMapperExtractor\Basic;

	use LeanMapper\Entity;


	/**
	 * @property Book[] $books m:belongsToMany
	 * @property Book[] $reviewedBooks m:belongsToMany(reviewer_id)
	 * @property string|NULL $web m:schemaComment(Absolute URL)
	 * @schema-option CHARSET UTF-8
	 */
	class Author extends Person
	{
	}
