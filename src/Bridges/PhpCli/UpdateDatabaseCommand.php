<?php

	namespace Inlm\SchemaGenerator\Bridges\PhpCli;

	use Inlm\SchemaGenerator;
	use CzProject\PhpCli\Application\ICommand;
	use CzProject\PhpCli\Console;


	class UpdateDatabaseCommand implements ICommand
	{
		/** @var SchemaGenerator\IIntegration */
		private $integration;


		public function __construct(SchemaGenerator\IIntegration $integration)
		{
			$this->integration = $integration;
		}


		public function getName()
		{
			return 'schema-update-database';
		}


		public function getDescription()
		{
			return 'Updates local database.';
		}


		public function getOptions()
		{
			return array();
		}


		public function run(Console $console, array $options, array $arguments)
		{
			$this->integration->updateDevelopmentDatabase();
		}
	}
