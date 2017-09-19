<?php

	namespace Inlm\SchemaGenerator;


	class DataType
	{
		/** @var string */
		private $type;

		/** @var array */
		private $parameters;

		/** @var array */
		private $options = array();


		/**
		 * @param  string
		 * @param  array|string|NULL
		 * @param  array  [OPTION => VALUE, OPTION2]
		 */
		public function __construct($type, array $parameters = NULL, array $options = array())
		{
			$this->type = $type;

			if ($parameters === NULL) {
				$parameters = array();

			} elseif (!is_array($parameters)) {
				$parameters = array($parameters);
			}

			$this->parameters = $parameters;

			foreach ($options as $k => $v) {
				if (is_int($k)) {
					$this->options[$v] = NULL;

				} else {
					$this->options[$k] = $v;
				}
			}
		}


		/**
		 * @return string
		 */
		public function getType()
		{
			return $this->type;
		}


		/**
		 * @return array
		 */
		public function getParameters()
		{
			return $this->parameters;
		}


		/**
		 * @return array
		 */
		public function getOptions()
		{
			return $this->options;
		}


		/**
		 * @param  string
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
