<?php

	namespace Inlm\SchemaGenerator;


	interface IAdapter
	{
		/**
		 * @return Configuration
		 */
		function load();

		/**
		 * @return void
		 */
		function save(Configuration $configuration);
	}
