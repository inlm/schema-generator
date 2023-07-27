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
		 * @param  string $file
		 */
		public function __construct($file)
		{
			$this->file = $file;
		}


		/**
		 * @return Schema
		 */
		public function generateSchema(array $options = [], array $customTypes = [], $databaseType = NULL)
		{
			$content = FileSystem::read($this->file);
			$config = [];

			if ($content !== '') {
				$config = Neon::decode($content);

				if (!is_array($config)) {
					throw new \Inlm\SchemaGenerator\InvalidStateException('Invalid config');
				}
			}

			return ConfigurationFactory::fromArray($config)->getSchema();
		}
	}
