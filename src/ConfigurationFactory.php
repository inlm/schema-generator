<?php

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema;


	class ConfigurationFactory
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @return Configuration
		 */
		public static function fromArray(array $config)
		{
			$schema = new SqlSchema\Schema;
			$configuration = new Configuration($schema);

			if (isset($config['options']) && is_array($config['options'])) {
				$configuration->setOptions($config['options']);
			}

			if (isset($config['schema']) && is_array($config['schema'])) {
				foreach ($config['schema'] as $definition) {
					self::createTable($schema, $definition);
				}
			}

			return $configuration;
		}


		private static function createTable(SqlSchema\Schema $schema, array $definition)
		{
			$table = $schema->addTable($definition['name']);

			if (isset($definition['comment'])) {
				$table->setComment($definition['comment']);
			}

			if (isset($definition['columns'])) {
				foreach ($definition['columns'] as $column) {
					$table->addColumn(self::createTableColumn($column));
				}
			}

			if (isset($definition['indexes'])) {
				foreach ($definition['indexes'] as $index) {
					$table->addIndex(self::createTableIndex($index));
				}
			}

			if (isset($definition['foreignKeys'])) {
				foreach ($definition['foreignKeys'] as $foreignKey) {
					$table->addForeignKey(self::createTableForeignKey($foreignKey));
				}
			}

			if (isset($definition['options'])) {
				foreach ($definition['options'] as $option => $optionValue) {
					$table->setOption($option, $optionValue);
				}
			}
		}


		private static function createTableColumn(array $definition)
		{
			$column = new SqlSchema\Column(
				$definition['name'],
				$definition['type'],
				isset($definition['parameters']) ? $definition['parameters'] : array(),
				isset($definition['options']) ? $definition['options'] : array()
			);
			$column->setNullable(isset($definition['nullable']) ? $definition['nullable'] : FALSE);
			$column->setAutoIncrement(isset($definition['autoIncrement']) ? $definition['autoIncrement'] : FALSE);
			$column->setDefaultValue(isset($definition['defaultValue']) ? $definition['defaultValue'] : NULL);
			$column->setComment(isset($definition['comment']) ? $definition['comment'] : NULL);
			return $column;
		}


		private static function createTableIndex(array $definition)
		{
			$index = new SqlSchema\Index($definition['name'], array(), $definition['type']);

			foreach ($definition['columns'] as $column) {
				$index->addColumn(self::createTableIndexColumn($column));
			}

			return $index;
		}


		private static function createTableIndexColumn(array $definition)
		{
			$order = isset($definition['order']) ? $definition['order'] : 'ASC';
			$column = new SqlSchema\IndexColumn($definition['name'], $order);
			$column->setLength(isset($definition['length']) ? $definition['length'] : NULL);
			return $column;
		}


		private static function createTableForeignKey(array $definition)
		{
			$foreignKey = new SqlSchema\ForeignKey($definition['name'], $definition['columns'], $definition['targetTable'], $definition['targetColumns']);

			if (isset($definition['onUpdateAction'])) {
				$foreignKey->setOnUpdateAction($definition['onUpdateAction']);
			}

			if (isset($definition['onDeleteAction'])) {
				$foreignKey->setOnDeleteAction($definition['onDeleteAction']);
			}

			return $foreignKey;
		}
	}
