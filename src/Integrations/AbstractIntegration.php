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
				$this->createLogger(),
				$this->getDatabaseType()
			);

			$this->applyOptions($generator);
			$this->applyCustomTypes($generator);

			$generator->setTestMode(TRUE);
			$generator->generate();
		}


		public function initFromDatabase()
		{
			$generator = new SchemaGenerator\SchemaGenerator(
				$this->createDatabaseExtractor(),
				$this->createAdapter(),
				$this->createSqlDumper(),
				$this->createLogger()
			);

			$this->applyOptions($generator);
			$this->applyCustomTypes($generator);

			$generator->setTestMode(FALSE);
			$generator->generate('init');
		}


		/**
		 * @return void
		 */
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


		/**
		 * @return void
		 */
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


		/**
		 * @return array<string, string|NULL>|NULL
		 */
		abstract protected function getOptions();


		/**
		 * @return array<string, string>
		 */
		abstract protected function getCustomTypes();


		/**
		 * @return string|NULL
		 */
		abstract protected function getDatabaseType();


		/**
		 * @return SchemaGenerator\IExtractor
		 */
		abstract protected function createExtractor();


		/**
		 * @return SchemaGenerator\IAdapter
		 */
		abstract protected function createAdapter();


		/**
		 * @return SchemaGenerator\IExtractor
		 */
		abstract protected function createDatabaseExtractor();


		/**
		 * @return SchemaGenerator\IAdapter
		 */
		abstract protected function createDatabaseAdapter();


		/**
		 * @return SchemaGenerator\IDumper
		 */
		abstract protected function createDatabaseDumper();


		/**
		 * @return SchemaGenerator\IDumper
		 */
		abstract protected function createSqlDumper();


		/**
		 * @return \CzProject\Logger\ILogger
		 */
		abstract protected function createLogger();
	}
