<?php

	namespace Inlm\SchemaGenerator\Utils;


	class PhpClass
	{
		/** @var string */
		private $name;

		/** @var bool */
		private $abstract;

		/** @var string|NULL */
		private $extends;

		/** @var string[] */
		private $implements;

		/** @var string|NULL */
		private $file;


		/**
		 * @param string $name
		 * @param bool $abstract
		 * @param string|NULL $extends
		 * @param string[] $implements
		 * @param string|NULL $file
		 */
		public function __construct(
			$name,
			$abstract,
			$extends,
			array $implements,
			$file
		)
		{
			$this->name = $name;
			$this->abstract = $abstract;
			$this->extends = $extends;
			$this->implements = $implements;
			$this->file = $file;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}


		/**
		 * @return bool
		 */
		public function isAbstract()
		{
			return $this->abstract;
		}


		/**
		 * @return bool
		 */
		public function hasParent()
		{
			return $this->extends !== NULL;
		}


		/**
		 * @return string|NULL
		 */
		public function getParent()
		{
			return $this->extends;
		}


		/**
		 * @param  string $class
		 * @return bool
		 */
		public function isExtend($class)
		{
			if ($this->extends === NULL) {
				return FALSE;
			}

			return strtolower($this->extends) === strtolower($class);
		}


		/**
		 * @param  string $class
		 * @return bool
		 */
		public function isImplement($class)
		{
			$class = strtolower($class);

			foreach ($this->implements as $implement) {
				if (strtolower($implement) === $class) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @return void
		 */
		public function loadFile()
		{
			if ($this->file === NULL) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('PHP class has no file defined.');
			}

			call_user_func(function ($file) { require $file; }, $this->file);
		}
	}
