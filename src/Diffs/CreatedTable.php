<?php

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class CreatedTable
	{
		/** @var SqlSchema\Table */
		private $definition;


		public function __construct(SqlSchema\Table $definition)
		{
			$this->definition = $definition;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->definition->getName();
		}


		/**
		 * @return SqlSchema\Table
		 */
		public function getDefinition()
		{
			return $this->definition;
		}
	}
