<?php

	namespace Inlm\SchemaGenerator\Bridges\PhpCli;

	use Inlm\SchemaGenerator;
	use CzProject\PhpCli\Application\ICommand;
	use CzProject\PhpCli\Console;


	class InitFromDatabaseCommand implements ICommand
	{
		/** @var SchemaGenerator\IIntegration */
		private $integration;


		public function __construct(SchemaGenerator\IIntegration $integration)
		{
			$this->integration = $integration;
		}


		public function getName()
		{
			return 'schema-init-from-database';
		}


		public function getDescription()
		{
			return 'Inits schema file & creates first SQL migrations from current database.';
		}


		public function getOptions()
		{
			return array();
		}


		public function run(Console $console, array $options, array $arguments)
		{
			$this->integration->initFromDatabase();
		}
	}
