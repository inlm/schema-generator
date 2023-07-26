<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;


	class GeneratorIndex
	{
		/** @var string */
		private $tableName;

		/** @var SqlSchema\Index */
		private $definition;


		/**
		 * @param  string $tableName
		 * @param  SqlSchema\Index $definition
		 */
		public function __construct($tableName, SqlSchema\Index $definition)
		{
			$this->tableName = $tableName;
			$this->definition = $definition;
		}


		/**
		 * @return SqlSchema\Index
		 */
		public function getDefinition()
		{
			return $this->definition;
		}


		/**
		 * @param  string $type
		 * @param  string|string[] $columns
		 * @return void
		 * @throws \Inlm\SchemaGenerator\IncompatibleException
		 */
		public function checkCompatibility($type, $columns)
		{
			$indexName = $this->definition->getName();

			if (!is_array($columns)) {
				$columns = [$columns];
			}

			$origType = $this->definition->getType();

			if ($origType !== $type) {
				throw new \Inlm\SchemaGenerator\IncompatibleException("Type mismatch for index '$indexName' in table '{$this->tableName}'. Original type '$origType', new type '$type'.");
			}

			$origColumns = [];

			foreach ($this->definition->getColumns() as $column) {
				$origColumns[] = $column->getName();
			}

			if ($origColumns !== $columns) {
				throw new \Inlm\SchemaGenerator\IncompatibleException("Mismatched columns for index '$indexName' in table '{$this->tableName}'. Original columns (" . implode(', ', $origColumns) . "), new columns (" . implode(', ', $columns) . ").");
			}
		}
	}
