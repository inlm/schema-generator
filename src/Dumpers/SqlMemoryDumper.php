<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;
	use Nette\Utils\Strings;


	class SqlMemoryDumper extends AbstractSqlDumper
	{
		/** @var SqlGenerator\IDriver */
		private $driver;


		/**
		 * @param  string|SqlGenerator\IDriver
		 */
		public function __construct($driver)
		{
			$this->driver = $this->prepareDriver($driver);
		}


		/**
		 * @return string
		 */
		public function getSql()
		{
			return $this->sqlDocument->toSql($this->driver);
		}


		/**
		 * @return void
		 */
		public function end()
		{
			$this->stop();
		}
	}
