<?php

	namespace Test\LeanMapperExtractor\SingleTableInheritance;

	use LeanMapper\Entity;


	/**
	 * @property string $companyName m:schemaType(varchar:200)
	 * @property string $ico m:schemaType(varchar:8)
	 * @property string $note m:schemaType(varchar:100)
	 */
	class UserCompany extends User
	{
	}
