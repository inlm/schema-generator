<?php

class Mapper extends \LeanMapper\DefaultMapper
{
	public function getEntityClass($table, LeanMapper\Row $row = NULL)
	{
		if ($table === 'client') {
			if (isset($row->type)) {
				return $row->type === Client::TYPE_INDIVIDUAL ? 'ClientIndividual' : 'ClientCompany';
			}

			return 'Client';
		}

		return parent::getEntityClass($table, $row);
	}


	public function getTable($entity)
	{
		if ($entity === 'ClientIndividual' || $entity === 'ClientCompany') {
			return 'client';
		}
		return parent::getTable();
	}
}
