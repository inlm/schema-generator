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


		public function __construct(SqlGenerator\IDriver $driver)
		{
			$this->driver = $driver;
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
		}
	}
