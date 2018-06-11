<?php

	namespace Test\LeanMapperExtractor\PrimaryKey;


	class Mapper extends \LeanMapper\DefaultMapper
	{
		public function getPrimaryKey($table)
		{
			if ($table === 'bookmeta') {
				return 'book_id';
			}
			return parent::getPrimaryKey($table);
		}
	}
