<?php

	namespace Test\LeanMapperExtractor\SingleTableInheritance;


	class Mapper extends \LeanMapper\DefaultMapper
	{
		public function getEntityClass($table, \LeanMapper\Row $row = NULL)
		{
			if ($table === 'user') {
				if ($row === NULL) {
					return User::class;
				}

				return $row->type === User::TYPE_COMPANY ? UserCompany::class : UserIndividual::class;
			}

			return parent::getEntityClass($table, $row);
		}


		public function getTable($entity)
		{
			if (is_subclass_of($entity, User::class, TRUE)) {
				return 'user';
			}
			return parent::getTable($entity);
		}
	}
