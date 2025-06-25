<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator;


	interface IIntegration
	{
		/**
		 * @param  string|NULL $description
		 * @param  bool $testMode
		 * @return void
		 */
		function createMigration($description = NULL, $testMode = FALSE);


		/**
		 * @param  bool $testMode
		 * @return void
		 */
		function updateDevelopmentDatabase($testMode = FALSE);


		/**
		 * @return void
		 */
		function showDiff();


		/**
		 * @return void
		 */
		function initFromDatabase();
	}
