<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Diffs;


	class RemovedTableOption
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $option;


		/**
		 * @param string $tableName
		 * @param string $option
		 */
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
