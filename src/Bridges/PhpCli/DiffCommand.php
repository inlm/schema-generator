<?php

	namespace Inlm\SchemaGenerator\Bridges\PhpCli;

	use Inlm\SchemaGenerator;
	use CzProject\PhpCli\Application\ICommand;
	use CzProject\PhpCli\Console;


	class DiffCommand implements ICommand
	{
		/** @var SchemaGenerator\IIntegration */
		private $integration;


		public function __construct(SchemaGenerator\IIntegration $integration)
		{
			$this->integration = $integration;
		}


		public function getName()
		{
			return 'schema-diff';
		}


		public function getDescription()
		{
			return 'Shows schema diff.';
		}


		public function getOptions()
		{
			return [];
		}


		public function run(Console $console, array $options, array $arguments)
		{
			$this->integration->showDiff();
		}
	}
