<?php

class Mapper extends \LeanMapper\DefaultMapper
{
	public function getEntityClass(string $table, LeanMapper\Row $row = NULL): string
	{
		if ($table === 'client') {
			if (isset($row->type)) {
				return $row->type === Client::TYPE_INDIVIDUAL ? 'ClientIndividual' : 'ClientCompany';
			}

			return 'Client';
		}

		return parent::getEntityClass($table, $row);
	}


	public function getTable(string $entity): string
	{
		if ($entity === 'ClientIndividual' || $entity === 'ClientCompany') {
			return 'client';
		}
		return parent::getTable();
	}
}
