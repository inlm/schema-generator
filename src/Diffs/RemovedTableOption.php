<?php

	namespace Inlm\SchemaGenerator\Diffs;


	class RemovedTableOption
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $option;


		public function __construct($tableName, $option)
		{
			$this->tableName = $tableName;
			$this->option = $option;
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
		public function getOption()
		{
			return $this->option;
		}
	}
