<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;
	use Nette\Utils\Strings;


	class SqlMemoryDumper extends AbstractSqlDumper
	{
		/** @var SqlGenerator\IDriver|string|NULL */
		private $driver;


		/**
		 * @param  SqlGenerator\IDriver|string|NULL
		 */
		public function __construct($driver = NULL)
		{
			$this->driver = $driver;
		}


		/**
		 * @return string
		 */
		public function getSql()
		{
			$driver = $this->prepareDriver($this->driver !== NULL ? $this->driver : $this->databaseType);
			return $this->sqlDocument->toSql($driver);
		}


		/**
		 * @return void
		 */
		public function end()
		{
			$this->stop();
		}
	}
