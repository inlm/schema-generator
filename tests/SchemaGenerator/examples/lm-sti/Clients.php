<?php

/**
 * @property int $id
 * @property string $type m:enum(self::TYPE_*)
 * @property string $name
 */
abstract class Client extends LeanMapper\Entity
{
	const TYPE_INDIVIDUAL = 'individual';
	const TYPE_COMPANY = 'company';
}


/**
 * @property \DateTime $birthdate
 * @property int $orders m:default(0)
 */
class ClientIndividual extends Client
{
	protected function initDefaults(): void
	{
		$this->type = self::TYPE_INDIVIDUAL;
	}
}


/**
 * @property string $ic
 * @property string $dic
 * @property int $orders m:default(0)
 */
class ClientCompany extends Client
{
	protected function initDefaults(): void
	{
		$this->type = self::TYPE_COMPANY;
	}
}
