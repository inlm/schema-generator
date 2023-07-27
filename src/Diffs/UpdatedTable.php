<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedTable
	{
		/** @var string */
		private $tableName;

		/** @var object[] */
		private $updates;


		/**
		 * @param  string $tableName
		 * @param  object[] $updates
		 */
		public function __construct($tableName, array $updates)
		{
			$this->tableName = $tableName;
			$this->updates = $updates;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}


		/**
		 * @return CreatedTableColumn[]
		 */
		public function getCreatedColumns()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\CreatedTableColumn');
		}


		/**
		 * @return UpdatedTableColumn[]
		 */
		public function getUpdatedColumns()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\UpdatedTableColumn');
		}


		/**
		 * @return RemovedTableColumn[]
		 */
		public function getRemovedColumns()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\RemovedTableColumn');
		}


		/**
		 * @return CreatedTableIndex[]
		 */
		public function getCreatedIndexes()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\CreatedTableIndex');
		}


		/**
		 * @return UpdatedTableIndex[]
		 */
		public function getUpdatedIndexes()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\UpdatedTableIndex');
		}


		/**
		 * @return RemovedTableIndex[]
		 */
		public function getRemovedIndexes()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\RemovedTableIndex');
		}


		/**
		 * @return CreatedForeignKey[]
		 */
		public function getCreatedForeignKeys()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\CreatedForeignKey');
		}


		/**
		 * @return UpdatedForeignKey[]
		 */
		public function getUpdatedForeignKeys()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\UpdatedForeignKey');
		}


		/**
		 * @return RemovedForeignKey[]
		 */
		public function getRemovedForeignKeys()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\RemovedForeignKey');
		}


		/**
		 * @return AddedTableOption[]
		 */
		public function getAddedOptions()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\AddedTableOption');
		}


		/**
		 * @return UpdatedTableOption[]
		 */
		public function getUpdatedOptions()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\UpdatedTableOption');
		}


		/**
		 * @return RemovedTableOption[]
		 */
		public function getRemovedOptions()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\RemovedTableOption');
		}


		/**
		 * @return UpdatedTableComment[]
		 */
		public function getUpdatedComments()
		{
			return $this->findUpdates('Inlm\SchemaGenerator\Diffs\UpdatedTableComment');
		}


		/**
		 * @template T of object
		 * @param  class-string<T> $class
		 * @return array<T>
		 */
		private function findUpdates($class)
		{
			$result = [];

			foreach ($this->updates as $update) {
				if ($update instanceof $class) {
					$result[] = $update;
				}
			}

			return $result;
		}
	}
