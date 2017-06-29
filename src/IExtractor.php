<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema\Schema;


	interface IExtractor
	{
		/**
		 * @return Schema
		 */
		function generateSchema(array $options, array $customTypes = array());
	}
