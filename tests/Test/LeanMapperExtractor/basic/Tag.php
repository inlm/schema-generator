<?php

	namespace Test\LeanMapperExtractor\Basic;

	use LeanMapper\Entity;


	/**
	 * @property int $id
	 * @property string $name m:schemaType(TEXT:20)
	 * @schema-comment Tags for books.
	 */
	class Tag extends Entity
	{
	}
