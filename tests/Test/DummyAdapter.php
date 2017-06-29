<?php

	namespace Test;

	use Inlm\SchemaGenerator\Configuration;
	use Inlm\SchemaGenerator\IAdapter;


	class DummyAdapter implements IAdapter
	{
		private $configuration;


		public function __construct(Configuration $configuration)
		{
			$this->configuration = $configuration;
		}


		/**
		 * @return Configuration
		 */
		public function load()
		{
			return $this->configuration;
		}

		/**
		 * @return void
		 */
		public function save(Configuration $configuration)
		{
		}
	}
