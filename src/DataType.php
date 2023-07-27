<?php

	namespace Inlm\SchemaGenerator;


	class DataType
	{
		/** @var string|NULL */
		private $type;

		/** @var scalar[]|NULL */
		private $parameters;

		/** @var array<string, scalar|NULL> */
		private $options = [];


		/**
		 * @param  string|NULL $type
		 * @param  scalar[]|NULL $parameters
		 * @param  array<string|int, scalar|NULL> $options  [OPTION => VALUE, OPTION2]
		 */
		public function __construct($type, array $parameters = NULL, array $options = [])
		{
			$this->type = $type !== NULL ? strtoupper($type) : NULL;
			$this->parameters = $parameters;

			foreach ($options as $k => $v) {
				if (is_int($k)) {
					$this->options[(string) $v] = NULL;

				} else {
					$this->options[$k] = $v;
				}
			}
		}


		/**
		 * @return string|NULL
		 */
		public function getType()
		{
			return $this->type;
		}


		/**
		 * @return scalar[]|NULL
		 */
		public function getParameters()
		{
			return $this->parameters;
		}


		/**
		 * @return array<string, scalar|NULL>
		 */
		public function getOptions()
		{
			return $this->options;
		}


		/**
		 * @param  string|NULL $type
		 * @param  scalar[]|NULL $parameters
		 * @param  array<string, scalar|NULL> $options
		 * @return bool
		 */
		public function isCompatible($type, array $parameters = NULL, array $options = NULL)
		{
			if ($this->type !== $type) {
				return FALSE;
			}

			if ($this->parameters !== $parameters) {
				return FALSE;
			}

			if ($this->options !== $options) {
				return FALSE;
			}

			return TRUE;
		}
	}
