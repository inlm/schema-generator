<?php

	namespace Test;

	use CzProject\SqlSchema\Schema;
	use Inlm\SchemaGenerator\IExtractor;


	class DummyExtractor implements IExtractor
	{
		/** @var Schema */
		private $schema;


		public function __construct(Schema $schema)
		{
			$this->schema = $schema;
		}


		/**
		 * @return Schema
		 */
		public function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL)
		{
			return $this->schema;
		}
	}
