<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedTableIndex
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\Index */
		private $definition;


		public function __construct($tableName, SqlSchema\Index $definition)
		{
			$this->tableName = $tableName;
			$this->definition = $definition;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}


		/**
		 * @return SqlSchema\Index
		 */
		public function getDefinition()
		{
			return $this->definition;
		}
	}
