<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedTableColumn
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $columnName;


		/**
		 * @param string $tableName
		 * @param string $columnName
		 */
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
