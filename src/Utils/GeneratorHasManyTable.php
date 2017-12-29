<?php

	namespace Inlm\SchemaGenerator\Utils;


	class GeneratorHasManyTable
	{
		/** @var string */
		private $sourceTable;

		/** @var string */
		private $sourceColumn;

		/** @var string */
		private $table;

		/** @var string */
		private $targetTable;

		/** @var string */
		private $targetColumn;


		/**
		 * @param  string
		 * @param  string
		 * @param  string
		 * @param  string
		 * @param  string
		 */
		public function __construct($sourceTable, $sourceColumn, $table, $targetTable, $targetColumn)
		{
			$this->sourceTable = $sourceTable;
			$this->sourceColumn = $sourceColumn;
			$this->table = $table;
			$this->targetTable = $targetTable;
			$this->targetColumn = $targetColumn;
		}


		/**
		 * @return string
		 */
		public function getSourceTable()
		{
			return $this->sourceTable;
		}


		/**
		 * @return string
		 */
		public function getSourceColumn()
		{
			return $this->sourceColumn;
		}


		/**
		 * @return string
		 */
		public function getTable()
		{
			return $this->table;
		}


		/**
		 * @return string
		 */
		public function getTargetTable()
		{
			return $this->targetTable;
		}


		/**
		 * @return string
		 */
		public function getTargetColumn()
		{
			return $this->targetColumn;
		}


		/**
		 * @return bool
		 */
		public function hasDifferences(GeneratorHasManyTable $hasManyTable)
		{
			return $hasManyTable->getTable() !== $this->table
				|| $hasManyTable->getSourceTable() !== $this->sourceTable
				|| $hasManyTable->getSourceColumn() !== $this->sourceColumn
				|| $hasManyTable->getTargetTable() !== $this->targetTable
				|| $hasManyTable->getTargetColumn() !== $this->targetColumn;
		}
	}
