<?php

	namespace Test\LeanMapperExtractor\CustomTypes;


	/**
	 * @property int $id
	 * @property string $name
	 * @property float $price
	 * @property float|NULL $sellPrice m:schemaType(money)
	 * @property Image $image
	 * @property \DateTime $pubdate
	 * @property bool $available
	 */
	class Book extends \LeanMapper\Entity
	{
	}
