<?php

	namespace Test\LeanMapperExtractor\PrimaryKey;


	class Mapper extends \LeanMapper\DefaultMapper
	{
		public function getPrimaryKey(string $table): string
		{
			if ($table === 'bookmeta' || $table === 'bookmeta2') {
				return 'book_id';
			}
			return parent::getPrimaryKey($table);
		}
	}
