<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedForeignKey
	{
		/** @var string */
		private $tableName;

		/** @var string|NULL */
		private $foreignKeyName;


		/**
		 * @param string $tableName
		 * @param string|NULL $foreignKeyName
		 */
		public function __construct($tableName, $foreignKeyName)
		{
			$this->tableName = $tableName;
			$this->foreignKeyName = $foreignKeyName;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}


		/**
		 * @return string|NULL
		 */
		public function getForeignKeyName()
		{
			return $this->foreignKeyName;
		}
	}
