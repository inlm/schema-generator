<?php

	namespace Test\LeanMapperExtractor\Basic;


	/**
	 * @property int $id
	 * @property Author $author m:hasOne
	 * @property Author|NULL $reviewer m:hasOne(reviewer_id)
	 * @property Tag[] $tags m:hasMany
	 * @property \DateTime $pubdate
	 * @property string $name
	 * @property string|NULL $description
	 * @property string|NULL $website
	 * @property bool $available m:schemaDefault(1)
	 * @property float|NULL $price
	 */
	class Book extends \LeanMapper\Entity
	{
	}
