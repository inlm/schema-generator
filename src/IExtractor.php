<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema\Schema;


	interface IExtractor
	{
		/**
		 * @param  array<string, scalar|NULL> $options
		 * @param  array<lowercase-string, DataType> $customTypes
		 * @param  string|NULL $databaseType
		 * @return Schema
		 */
		function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL);
	}
