<?php

	namespace Test\LeanMapperExtractor\SingleTableInheritance;


	/**
	 * @property string $firstName m:schemaType(varchar:100)
	 * @property string $lastName m:schemaType(varchar:100)
	 * @property string $note m:schemaType(varchar:100)
	 */
	class UserIndividual extends User
	{
	}
