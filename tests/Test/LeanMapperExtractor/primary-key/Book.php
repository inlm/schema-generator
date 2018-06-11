<?php

	namespace Test\LeanMapperExtractor\PrimaryKey;


	/**
	 * @property int $id
	 * @property string $name
	 * @property BookMeta|NULL $meta m:belongsToOne
	 */
	class Book extends \LeanMapper\Entity
	{
	}
