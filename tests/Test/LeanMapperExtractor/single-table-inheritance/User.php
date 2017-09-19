<?php

	namespace Test\LeanMapperExtractor\SingleTableInheritance;


	/**
	 * @property int $id
	 * @property int $type m:enum(self::TYPE_*) m:schemaType(tinyint unsigned)
	 * @property \DateTimeInterface $created
	 * @property \DateTimeInterface|NULL $updated
	 */
	abstract class User extends \LeanMapper\Entity
	{
		const TYPE_INDIVIDUAL = 0;
		const TYPE_COMPANY = 1;
	}
