<?php

	namespace Inlm\SchemaGenerator;


	interface IIntegration
	{
		/**
		 * @param  string|NULL
		 * @param  bool
		 * @return void
		 */
		function createMigration($description = NULL, $testMode = FALSE);


		/**
		 * @param  bool
		 * @return void
		 */
		function updateDevelopmentDatabase($testMode = FALSE);


		/**
		 * @return void
		 */
		function showDiff();
	}
