<?php

	namespace Inlm\SchemaGenerator\Integrations;

	use Inlm\SchemaGenerator;
	use LeanMapper;


	abstract class AbstractIntegration implements SchemaGenerator\IIntegration
	{
		public function createMigration($description = NULL, $testMode = FALSE)
		{
			$generator = new SchemaGenerator\SchemaGenerator(
				$this->createExtractor(),
				$this->createAdapter(),
				$this->createSqlDumper(),
				$this->createLogger()
			);

			$this->applyOptions($generator);
			$this->applyCustomTypes($generator);

			$generator->setTestMode($testMode);
			$generator->generate($description);
		}


		public function updateDevelopmentDatabase($testMode = FALSE)
		{
			$generator = new SchemaGenerator\SchemaGenerator(
				$this->createExtractor(),
				$this->createDatabaseAdapter(),
				$this->createDatabaseDumper(),
				$this->createLogger()
			);

			$this->applyOptions($generator);
			$this->applyCustomTypes($generator);

			$generator->setTestMode($testMode);
			$generator->generate();
		}


		public function showDiff()
		{
			$generator = new SchemaGenerator\SchemaGenerator(
				$this->createExtractor(),
				$this->createAdapter(),
				new SchemaGenerator\Dumpers\NullDumper,
				$this->createLogger()
			);

			$this->applyOptions($generator);
			$this->applyCustomTypes($generator);

			$generator->setTestMode(TRUE);
			$generator->generate();
		}


		protected function applyOptions(SchemaGenerator\SchemaGenerator $generator)
		{
			$options = $this->getOptions();

			if ($options === NULL) {
				return;
			}

			foreach ($options as $option => $value) {
				$generator->setOption($option, $value);
			}
		}


		protected function applyCustomTypes(SchemaGenerator\SchemaGenerator $generator)
		{
			$customTypes = $this->getCustomTypes();

			if ($customTypes === NULL) {
				return;
			}

			foreach ($customTypes as $name => $definition) {
				$type = SchemaGenerator\Utils\DataTypeParser::parse($definition);
				$generator->setCustomType($name, $type->getType(), $type->getParameters(), $type->getOptions());
			}
		}


		abstract protected function getOptions();


		abstract protected function getCustomTypes();


		abstract protected function createExtractor();


		abstract protected function createAdapter();


		abstract protected function createDatabaseAdapter();


		abstract protected function createDatabaseDumper();


		abstract protected function createSqlDumper();


		abstract protected function createLogger();
	}
