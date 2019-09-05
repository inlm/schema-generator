<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema\Schema;


	interface IExtractor
	{
		/**
		 * @param  array
		 * @param  array
		 * @param  string|NULL
		 * @return Schema
		 */
		function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL);
	}
