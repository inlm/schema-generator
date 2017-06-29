<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedTableColumn
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\Column */
		private $definition;


		public function __construct($tableName, SqlSchema\Column $definition)
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
		 * @return SqlSchema\Column
		 */
		public function getDefinition()
		{
			return $this->definition;
		}
	}
