<?php

	namespace Test\LeanMapperExtractor\PrimaryKey;


	/**
	 * @property Book $bookId m:hasOne(book_id)
	 * @property int $year
	 */
	class BookMeta extends \LeanMapper\Entity
	{
	}
