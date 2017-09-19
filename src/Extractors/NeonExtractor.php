<?php

	namespace Inlm\SchemaGenerator\Extractors;

	use CzProject\SqlSchema\Schema;
	use Inlm\SchemaGenerator\ConfigurationFactory;
	use Inlm\SchemaGenerator\IExtractor;
	use Nette\Neon\Neon;
	use Nette\Utils\FileSystem;


	class NeonExtractor implements IExtractor
	{
		/** @var string */
		private $file;


		/**
		 * @param  string
		 */
		public function __construct($file)
		{
			$this->file = $file;
		}


		/**
		 * @return Schema
		 */
		public function generateSchema(array $options = array(), array $customTypes = array())
		{
			$content = FileSystem::read($this->file);
			$config = array();

			if ($content !== '') {
				$config = Neon::decode($content);
			}

			return ConfigurationFactory::fromArray($config)->getSchema();
		}
	}
