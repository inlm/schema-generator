<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Dumpers;

	use CzProject\SqlGenerator;
	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Diffs;
	use Inlm\SchemaGenerator\IDumper;
	use Nette\Utils\Strings;


	class SqlDumper extends AbstractSqlDumper
	{
		const FLAT = 0;
		const YEAR = 1;
		const YEAR_MONTH = 2;

		/** @var string */
		private $directory;

		/** @var int */
		private $outputStructure = self::FLAT;


		/**
		 * @param  string $directory
		 */
		public function __construct($directory)
		{
			$this->directory = $directory;
		}


		/**
		 * @param  int $outputStructure
		 * @return static
		 */
		public function setOutputStructure($outputStructure)
		{
			$this->outputStructure = $outputStructure;
			return $this;
		}


		/**
		 * @return static
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
			$this->checkIfStarted();

			if (!$this->sqlDocument->isEmpty()) {
				$driver = $this->prepareDriver($this->databaseType);
				$directory = $this->directory;

				if ($this->outputStructure === self::YEAR_MONTH) {
					$directory .= '/' . date('Y/m');

				} elseif ($this->outputStructure === self::YEAR) {
					$directory .= '/' . date('Y');

				} elseif ($this->outputStructure !== self::FLAT) {
					throw new \Inlm\SchemaGenerator\InvalidArgumentException("Invalid output structure '{$this->outputStructure}'.");
				}

				$description = '';

				if (isset($this->description)) {
					$description = Strings::webalize($this->description);

					if ($description !== '') {
						$description = '-' . $description;
					}
				}

				$path = $directory . '/' . date('Y-m-d-His') . $description . '.sql';

				if (file_exists($path)) {
					throw new \Inlm\SchemaGenerator\FileSystemException("File '$path' already exists.");
				}

				@mkdir($directory, 0777, TRUE);
				file_put_contents($path, $this->getHeaderBlock() . $this->sqlDocument->toSql($driver));
			}

			$this->stop();
		}
	}
