<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Adapters;

	use CzProject\SqlSchema\Schema;
	use Inlm\SchemaGenerator\IAdapter;
	use Inlm\SchemaGenerator\Configuration;


	class MemoryAdapter implements IAdapter
	{
		/** @var Schema */
		private $schema;


		public function __construct(?Schema $schema = NULL)
		{
			$this->schema = $schema ? $schema : new Schema;
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
