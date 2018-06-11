<?php

	namespace Test\LeanMapperExtractor\PrimaryKey;


	/**
	 * @property int $id
	 * @property string $name
	 * @property BookMeta|NULL $meta m:belongsToOne
	 * @property BookMeta2|NULL $meta2 m:belongsToOne
	 */
	class Book extends \LeanMapper\Entity
	{
	}
