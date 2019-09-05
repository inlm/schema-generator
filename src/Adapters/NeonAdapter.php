<?php

	namespace Inlm\SchemaGenerator\Adapters;

	use Inlm\SchemaGenerator\IAdapter;
	use Inlm\SchemaGenerator\Configuration;
	use Inlm\SchemaGenerator\ConfigurationFactory;
	use Inlm\SchemaGenerator\ConfigurationSerializer;
	use Nette\Utils\FileSystem;
	use Nette\Neon\Neon;


	class NeonAdapter implements IAdapter
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
		 * @return Configuration
		 */
		public function load()
		{
			$config = [];

			if (file_exists($this->file)) {
				$content = FileSystem::read($this->file);

				if ($content !== '') {
					$config = Neon::decode($content);
				}
			}

			return ConfigurationFactory::fromArray($config);
		}


		/**
		 * @return void
		 */
		public function save(Configuration $configuration)
		{
			$content = Neon::encode(ConfigurationSerializer::serialize($configuration), Neon::BLOCK);
			FileSystem::write($this->file, "# DON'T EDIT THIS FILE!\n\n" . rtrim($content) . "\n", NULL);
		}
	}
