<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class RemovedTableIndex
	{
		/** @var string */
		private $tableName;

		/** @var string|NULL */
		private $indexName;


		/**
		 * @param string $tableName
		 * @param string|NULL $indexName
		 */
		public function __construct($tableName, $indexName)
		{
			$this->tableName = $tableName;
			$this->indexName = $indexName;
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
		public function getIndexName()
		{
			return $this->indexName;
		}
	}
