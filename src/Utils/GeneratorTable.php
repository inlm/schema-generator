<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;


	class GeneratorTable
	{
		/** @var SqlSchema\Table */
		private $definition;

		/** @var string|NULL */
		private $primaryColumn;

		/** @var int */
		private $created = 0;


		/**
		 * @param  SqlSchema\Table
		 * @param  string|NULL
		 */
		public function __construct(SqlSchema\Table $definition, $primaryColumn = NULL)
		{
			$this->definition = $definition;
			$this->primaryColumn = $primaryColumn;
		}


		/**
		 * @return SqlSchema\Table
		 */
		public function getDefinition()
		{
			return $this->definition;
		}


		/**
		 * @return string|NULL
		 */
		public function getPrimaryColumn()
		{
			return $this->primaryColumn;
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
