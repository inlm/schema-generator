<?php

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;


	class SqlDumper extends AbstractSqlDumper
	{
		const FLAT = 0;
		const YEAR = 1;
		const YEAR_MONTH = 2;

		/** @var string */
		private $directory;

		/** @var SqlGenerator\IDriver */
		private $driver;

		/** @var int */
		private $outputStructure = self::FLAT;


		/**
		 * @param  string
		 */
		public function __construct($directory, SqlGenerator\IDriver $driver)
		{
			$this->directory = $directory;
			$this->driver = $driver;
		}


		/**
		 * @param  int
		 * @return self
		 */
		public function setOutputStructure($outputStructure)
		{
			$this->outputStructure = $outputStructure;
			return $this;
		}


		/**
		 * @return self
		 * @deprecated
		 */
		public function setDeepStructure()
		{
			$this->outputStructure = self::YEAR_MONTH;
			return $this;
		}


		/**
		 * @return void
		 */
		public function end()
		{
			if (!$this->sqlDocument->isEmpty()) {
				$directory = $this->directory;

				if ($this->outputStructure === self::YEAR_MONTH) {
					$directory .= '/' . date('Y/m');

				} elseif ($this->outputStructure === self::YEAR) {
					$directory .= '/' . date('Y');

				} elseif ($this->outputStructure !== self::FLAT) {
					throw new \Inlm\SchemaGenerator\InvalidArgumentException("Invalid output structure '{$this->outputStructure}'.");
				}

				@mkdir($directory, 0777, TRUE);
				file_put_contents($directory . '/' . date('Y-m-d-His') . '.sql', $this->sqlDocument->toSql($this->driver));
			}
			$this->sqlDocument = NULL;
		}
	}
