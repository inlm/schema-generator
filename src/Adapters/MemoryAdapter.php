<?php

	namespace Inlm\SchemaGenerator\Adapters;

	use CzProject\SqlSchema\Schema;
	use Inlm\SchemaGenerator\IAdapter;
	use Inlm\SchemaGenerator\Configuration;


	class MemoryAdapter implements IAdapter
	{
		/** @var Schema */
		private $schema;


		public function __construct(Schema $schema)
		{
			$this->schema = $schema;
		}


		/**
		 * @return Configuration
		 */
		public function load()
		{
			return new Configuration($this->schema);
		}


		/**
		 * @return void
		 */
		public function save(Configuration $configuration)
		{
		}
	}
