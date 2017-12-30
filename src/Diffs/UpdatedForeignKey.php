<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedForeignKey
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\ForeignKey */
		private $definition;


		public function __construct($tableName, SqlSchema\ForeignKey $definition)
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
		 * @return string
		 */
		public function getForeignKeyName()
		{
			return $this->definition->getName();
		}


		/**
		 * @return SqlSchema\ForeignKey
		 */
		public function getDefinition()
		{
			return $this->definition;
		}
	}
