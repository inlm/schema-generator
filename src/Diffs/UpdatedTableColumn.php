<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedTableColumn
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\Column */
		private $definition;

		/** @var string|NULL */
		private $afterColumn;

		/** @var bool */
		private $onlyPositionChange;


		public function __construct($tableName, SqlSchema\Column $definition, $afterColumn = NULL, $onlyPositionChange = FALSE)
		{
			$this->tableName = $tableName;
			$this->definition = $definition;
			$this->afterColumn = $afterColumn;
			$this->onlyPositionChange = $onlyPositionChange;
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


		/**
		 * @return bool
		 */
		public function hasOnlyPositionChange()
		{
			return $this->onlyPositionChange;
		}
	}
