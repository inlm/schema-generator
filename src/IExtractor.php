<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema\Schema;


	interface IExtractor
	{
		/**
		 * @param  array $options
		 * @param  array $customTypes
		 * @param  string|NULL $databaseType
		 * @return Schema
		 */
		function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL);
	}
