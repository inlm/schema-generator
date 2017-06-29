<?php

	namespace Inlm\SchemaGenerator\Diffs;


	class UpdatedTableOption
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $option;

		/** @var mixed|NULL */
		private $value;


		public function __construct($tableName, $option, $value = NULL)
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
		 * @return mixed|NULL
		 */
		public function getValue()
		{
			return $this->value;
		}
	}
