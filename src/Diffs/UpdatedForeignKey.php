<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator\Diffs;

	use CzProject\SqlSchema;


	class UpdatedForeignKey
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\ForeignKey */
		private $definition;


		/**
		 * @param string $tableName
		 */
		public function __construct($tableName, SqlSchema\ForeignKey $definition)
		{
			$this->tableName = $tableName;
			$this->definition = $definition;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}


		/**
		 * @return string|NULL
		 */
		public function getForeignKeyName()
		{
			return $this->definition->getName();
		}


		/**
		 * @return SqlSchema\ForeignKey
		 */
		public function getDefinition()
		{
			return $this->definition;
		}
	}
