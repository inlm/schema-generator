<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema\Schema;


	class Configuration
	{
		/** @var Schema */
		private $schema;

		/** @var array<string, string> */
		private $options = [];


		public function __construct(Schema $schema)
		{
			$this->schema = $schema;
		}


		/**
		 * @return Schema
		 */
		public function getSchema()
		{
			return $this->schema;
		}


		/**
		 * @return array<string, string>
		 */
		public function getOptions()
		{
			return $this->options;
		}


		/**
		 * @param  array<string, string> $options
		 * @return static
		 */
		public function setOptions(array $options)
		{
			foreach ($options as $option => $value) {
				$this->setOption($option, $value);
			}

			return $this;
		}


		/**
		 * @param  string $option
		 * @param  string $value
		 * @return static
		 */
		public function setOption($option, $value)
		{
			$this->options[$option] = $value;
			return $this;
		}
	}
