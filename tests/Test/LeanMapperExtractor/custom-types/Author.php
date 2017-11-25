<?php

	namespace Test\LeanMapperExtractor\CustomTypes;


	/**
	 * @property int $id
	 * @property string $name m:schemaType(varchar:100)
	 * @property int $age m:schemaType(unsigned)
	 * @property Image $photo
	 */
	class Author extends \LeanMapper\Entity
	{
	}
