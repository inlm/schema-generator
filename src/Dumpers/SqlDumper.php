<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;


	class SqlDumper extends AbstractSqlDumper
	{
		/** @var string */
		private $directory;

		/** @var SqlGenerator\IDriver */
		private $driver;

		/** @var bool */
		private $deepStructure = FALSE;


		/**
		 * @param  string
		 */
		public function __construct($directory, SqlGenerator\IDriver $driver)
		{
			$this->directory = $directory;
			$this->driver = $driver;
		}


		/**
		 * @return self
		 */
		public function setDeepStructure()
		{
			$this->deepStructure = TRUE;
			return $this;
		}


		/**
		 * @return void
		 */
		public function end()
		{
			if (!$this->sqlDocument->isEmpty()) {
				$directory = $this->directory;

				if ($this->deepStructure) {
					$directory .= '/' . date('Y/m');
				}

				@mkdir($directory, 0777, TRUE);
				file_put_contents($directory . '/' . date('Y-m-d-His') . '.sql', $this->sqlDocument->toSql($this->driver));
			}
			$this->sqlDocument = NULL;
		}
	}
