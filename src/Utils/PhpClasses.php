<?php

	namespace Inlm\SchemaGenerator\Utils;


	class PhpClasses
	{
		/** @var array<string, PhpClass> */
		private $classes = [];


		/**
		 * @return void
		 */
		public function addClass(PhpClass $class)
		{
			$name = strtolower($class->getName());

			if (isset($this->classes[$name])) {
				throw new \Inlm\SchemaGenerator\InvalidArgumentException("Class $name already exists.");
			}

			$this->classes[$name] = $class;
		}


		/**
		 * @return array<string, PhpClass>
		 */
		public function getClasses()
		{
			return $this->classes;
		}


		/**
		 * @param  string $class
		 * @return bool
		 */
		public function hasClass($class)
		{
			$class = strtolower($class);
			return isset($this->classes[$class]);
		}


		/**
		 * @param  string $class
		 * @return PhpClass
		 */
		public function getClass($class)
		{
			$key = strtolower($class);

			if (!isset($this->classes[$key])) {
				throw new \Inlm\SchemaGenerator\MissingException("Missing class $class.");
			}

			return $this->classes[$key];
		}


		/**
		 * @param  string $superClass
		 * @return bool
		 */
		public function isSubclassOf(PhpClass $class, $superClass)
		{
			do {
				if ($class->extendsClass($superClass)) {
					return TRUE;
				}

				if ($class->implementsInterface($superClass)) {
					return TRUE;
				}

				if (!$class->hasParent()) {
					return FALSE;
				}

				$parentClass = $class->getParent();

				if ($parentClass === NULL) {
					return FALSE;
				}

				if (!$this->hasClass($parentClass)) {
					return FALSE;
				}

				$class = $this->getClass($parentClass);

			} while (TRUE);
		}
	}
