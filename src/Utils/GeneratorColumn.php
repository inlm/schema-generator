<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;


	class GeneratorColumn
	{
		/** @var SqlSchema\Column */
		private $definition;

		/** @var int */
		private $created = 0;


		/**
		 * @param  SqlSchema\Column $definition
		 */
		public function __construct(SqlSchema\Column $definition)
		{
			$this->definition = $definition;
		}


		/**
		 * @return SqlSchema\Column
		 */
		public function getDefinition()
		{
			return $this->definition;
		}


		/**
		 * @return void
		 */
		public function markAsCreated()
		{
			$this->created++;
		}


		/**
		 * @return int
		 */
		public function getNumberOfCreation()
		{
			return $this->created;
		}
	}
