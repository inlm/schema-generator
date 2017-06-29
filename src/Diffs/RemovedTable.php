<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedTable
	{
		/** @var string */
		private $tableName;


		public function __construct($tableName)
		{
			$this->tableName = $tableName;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}
	}
