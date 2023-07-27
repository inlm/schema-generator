<?php

	namespace Inlm\SchemaGenerator\Diffs;


	class UpdatedTableOption
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $option;

		/** @var string */
		private $value;


		/**
		 * @param string $tableName
		 * @param string $option
		 * @param string $value
		 */
		public function __construct($tableName, $option, $value)
		{
			$this->tableName = $tableName;
			$this->option = $option;
			$this->value = $value;
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


		/**
		 * @return string
		 */
		public function getValue()
		{
			return $this->value;
		}
	}
