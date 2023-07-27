<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedForeignKey
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $foreignKeyName;


		/**
		 * @param string $tableName
		 * @param string $foreignKeyName
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
		 * @return string
		 */
		public function getForeignKeyName()
		{
			return $this->foreignKeyName;
		}
	}
