<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedTableColumn
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $columnName;


		public function __construct($tableName, $columnName)
		{
			$this->tableName = $tableName;
			$this->columnName = $columnName;
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
		public function getColumnName()
		{
			return $this->columnName;
		}
	}
