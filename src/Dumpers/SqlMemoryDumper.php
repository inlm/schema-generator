<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;
	use Nette\Utils\Strings;


	class SqlMemoryDumper extends AbstractSqlDumper
	{
		/**
		 * @return string
		 */
		public function getSql()
		{
			$driver = $this->prepareDriver($this->databaseType);
			return (!$this->sqlDocument->isEmpty() ? $this->getHeaderBlock() : '') . $this->sqlDocument->toSql($driver);
		}


		/**
		 * @return void
		 */
		public function end()
		{
			$this->stop();
		}
	}
