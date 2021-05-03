<?php

	namespace Inlm\SchemaGenerator\Utils;


	class PhpClassFinder
	{
		/** @var string[] */
		private $directories;


		public function __construct(array $directories)
		{
			$this->directories = $directories;
		}


		/**
		 * @return PhpClasses
		 */
		public function find()
		{
			$classes = new PhpClasses;
			$iterator = new \AppendIterator;

			foreach ($this->directories as $directory) {
				$iterator->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), \RecursiveIteratorIterator::SELF_FIRST));
			}

			foreach ($iterator as $file) {
				if ($file->getExtension() !== 'php') {
					continue;
				}

				$parser = PhpClassParser::fromFile((string) $file);

				foreach ($parser->parse() as $class) {
					$classes->addClass($class);
				}
			}

			return $classes;
		}
	}
