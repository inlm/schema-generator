<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;


	class NullDumper implements IDumper
	{
		/**
		 * @param  string|NULL
		 * @param  string|NULL
		 * @return void
		 */
		public function start($description = NULL, $databaseType = NULL)
		{
		}


		/**
		 * @return void
		 */
		public function end()
		{
		}


		/**
		 * @return void
		 */
		public function createTable(Diffs\CreatedTable $table)
		{
		}


		/**
		 * @return void
		 */
		public function removeTable(Diffs\RemovedTable $table)
		{
		}


		/**
		 * @return void
		 */
		public function createTableColumn(Diffs\CreatedTableColumn $column)
		{
		}


		/**
		 * @return void
		 */
		public function updateTableColumn(Diffs\UpdatedTableColumn $column)
		{
		}


		/**
		 * @return void
		 */
		public function removeTableColumn(Diffs\RemovedTableColumn $column)
		{
		}


		/**
		 * @return void
		 */
		public function createTableIndex(Diffs\CreatedTableIndex $index)
		{
		}


		/**
		 * @return void
		 */
		public function updateTableIndex(Diffs\UpdatedTableIndex $index)
		{
		}


		/**
		 * @return void
		 */
		public function removeTableIndex(Diffs\RemovedTableIndex $index)
		{
		}


		/**
		 * @return void
		 */
		public function createForeignKey(Diffs\CreatedForeignKey $foreignKey)
		{
		}


		/**
		 * @return void
		 */
		public function updateForeignKey(Diffs\UpdatedForeignKey $foreignKey)
		{
		}


		/**
		 * @return void
		 */
		public function removeForeignKey(Diffs\RemovedForeignKey $foreignKey)
		{
		}


		/**
		 * @return void
		 */
		public function addTableOption(Diffs\AddedTableOption $option)
		{
		}


		/**
		 * @return void
		 */
		public function updateTableOption(Diffs\UpdatedTableOption $option)
		{
		}


		/**
		 * @return void
		 */
		public function removeTableOption(Diffs\RemovedTableOption $option)
		{
		}


		/**
		 * @return void
		 */
		public function updateTableComment(Diffs\UpdatedTableComment $comment)
		{
		}
	}
