<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class CreatedTableColumn
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\Column */
		private $definition;

		/** @var string|NULL */
		private $afterColumn;


		/**
		 * @param string $tableName
		 * @param string|NULL $afterColumn
		 */
		public function __construct($tableName, SqlSchema\Column $definition, $afterColumn = NULL)
		{
			$this->tableName = $tableName;
			$this->definition = $definition;
			$this->afterColumn = $afterColumn;
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


		/**
		 * @return string|NULL
		 */
		public function getAfterColumn()
		{
			return $this->afterColumn;
		}
	}
