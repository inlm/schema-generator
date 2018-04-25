<?php

	namespace Inlm\SchemaGenerator;

	use Inlm\SchemaGenerator\Diffs;


	interface IDumper
	{
		/**
		 * @param  string  see Database::*
		 * @param  string|NULL
		 * @return void
		 */
		function start($databaseType, $description = NULL);


		/**
		 * @return void
		 */
		function end();


		/**
		 * @return void
		 */
		function createTable(Diffs\CreatedTable $table);


		/**
		 * @return void
		 */
		function removeTable(Diffs\RemovedTable $table);


		/**
		 * @return void
		 */
		function createTableColumn(Diffs\CreatedTableColumn $column);


		/**
		 * @return void
		 */
		function updateTableColumn(Diffs\UpdatedTableColumn $column);


		/**
		 * @return void
		 */
		function removeTableColumn(Diffs\RemovedTableColumn $column);


		/**
		 * @return void
		 */
		function createTableIndex(Diffs\CreatedTableIndex $index);


		/**
		 * @return void
		 */
		function updateTableIndex(Diffs\UpdatedTableIndex $index);


		/**
		 * @return void
		 */
		function removeTableIndex(Diffs\RemovedTableIndex $index);


		/**
		 * @return void
		 */
		function createForeignKey(Diffs\CreatedForeignKey $foreignKey);


		/**
		 * @return void
		 */
		function updateForeignKey(Diffs\UpdatedForeignKey $foreignKey);


		/**
		 * @return void
		 */
		function removeForeignKey(Diffs\RemovedForeignKey $foreignKey);


		/**
		 * @return void
		 */
		function addTableOption(Diffs\AddedTableOption $option);


		/**
		 * @return void
		 */
		function updateTableOption(Diffs\UpdatedTableOption $option);


		/**
		 * @return void
		 */
		function removeTableOption(Diffs\RemovedTableOption $option);


		/**
		 * @return void
		 */
		function updateTableComment(Diffs\UpdatedTableComment $comment);
	}
