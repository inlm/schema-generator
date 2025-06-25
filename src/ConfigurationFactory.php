<?php

	declare(strict_types=1);

	namespace Inlm\SchemaGenerator;

	use CzProject\SqlSchema;


	class ConfigurationFactory
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @param  array<string, mixed> $config
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
				foreach ($config['schema'] as $tableName => $definition) {
					$definition['name'] = isset($definition['name']) ? $definition['name'] : $tableName;
					self::createTable($schema, $definition);
				}
			}

			return $configuration;
		}


		/**
		 * @param  array<string, mixed> $definition
		 * @return void
		 */
		private static function createTable(SqlSchema\Schema $schema, array $definition)
		{
			$table = $schema->addTable(self::getString($definition, 'name'));

			if (isset($definition['comment'])) {
				$table->setComment(self::getStringOrNull($definition, 'comment'));
			}

			if (isset($definition['columns'])) {
				foreach (self::getArray($definition, 'columns') as $columnName => $column) {
					self::checkArray($column);
					$column['name'] = isset($column['name']) ? $column['name'] : $columnName;
					$table->addColumn(self::createTableColumn($column));
				}
			}

			if (isset($definition['indexes'])) {
				foreach (self::getArray($definition, 'indexes') as $indexName => $index) {
					self::checkArray($index);
					$index['name'] = isset($index['name']) ? $index['name'] : $indexName;
					$table->addIndex(self::createTableIndex($index));
				}
			}

			if (isset($definition['foreignKeys'])) {
				foreach (self::getArray($definition, 'foreignKeys') as $foreignKeyName => $foreignKey) {
					self::checkArray($foreignKey);
					$foreignKey['name'] = isset($foreignKey['name']) ? $foreignKey['name'] : $foreignKeyName;
					$table->addForeignKey(self::createTableForeignKey($foreignKey));
				}
			}

			if (isset($definition['options'])) {
				foreach (self::getArray($definition, 'options') as $option => $optionValue) {
					$table->setOption($option, self::string($optionValue, 'options.' . $option));
				}
			}
		}


		/**
		 * @param  array<string, mixed|NULL> $definition
		 * @return SqlSchema\Column
		 */
		private static function createTableColumn(array $definition)
		{
			$column = new SqlSchema\Column(
				self::getString($definition, 'name'),
				self::getStringOrNull($definition, 'type'),
				isset($definition['parameters']) && is_array($definition['parameters']) ? $definition['parameters'] : [],
				isset($definition['options']) && is_array($definition['options']) ? $definition['options'] : []
			);
			$column->setNullable(isset($definition['nullable']) ? self::getBool($definition, 'nullable') : FALSE);
			$column->setAutoIncrement(isset($definition['autoIncrement']) ? self::getBool($definition, 'autoIncrement') : FALSE);

			if (isset($definition['defaultValue'])) {
				$column->setDefaultValue(self::getScalarOrNull($definition, 'defaultValue'));
			}

			$column->setComment(isset($definition['comment']) ? self::getString($definition, 'comment') : NULL);
			return $column;
		}


		/**
		 * @param  array<string, mixed> $definition
		 * @return SqlSchema\Index
		 */
		private static function createTableIndex(array $definition)
		{
			$indexName = self::getStringOrNull($definition, 'name');
			$index = new SqlSchema\Index($indexName !== '' ? $indexName : NULL, [], self::getString($definition, 'type'));

			foreach (self::getArray($definition, 'columns') as $column) {
				self::checkArray($column);
				$index->addColumn(self::createTableIndexColumn($column));
			}

			return $index;
		}


		/**
		 * @param  array<string, mixed> $definition
		 * @return SqlSchema\IndexColumn
		 */
		private static function createTableIndexColumn(array $definition)
		{
			$order = isset($definition['order']) ? self::getString($definition, 'order') : 'ASC';
			$column = new SqlSchema\IndexColumn(self::getString($definition, 'name'), $order);
			$column->setLength(isset($definition['length']) ? self::getInt($definition, 'length') : NULL);
			return $column;
		}


		/**
		 * @param  array<string, mixed> $definition
		 * @return SqlSchema\ForeignKey
		 */
		private static function createTableForeignKey(array $definition)
		{
			$foreignKey = new SqlSchema\ForeignKey(
				self::getStringOrNull($definition, 'name'),
				self::getStringOrList($definition, 'columns'),
				self::getStringOrNull($definition, 'targetTable'),
				self::getStringOrList($definition, 'targetColumns')
			);

			if (isset($definition['onUpdateAction'])) {
				$foreignKey->setOnUpdateAction(self::getString($definition, 'onUpdateAction'));
			}

			if (isset($definition['onDeleteAction'])) {
				$foreignKey->setOnDeleteAction(self::getString($definition, 'onDeleteAction'));
			}

			return $foreignKey;
		}


		/**
		 * @param  mixed|NULL $value
		 * @return array<string, mixed>
		 * @phpstan-assert array<string, mixed> $value
		 */
		private static function checkArray($value)
		{
			if (is_array($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Definition must be array, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  mixed|NULL $value
		 * @param  string $fieldName
		 * @return string
		 * @phpstan-assert string $value
		 */
		private static function string($value, $fieldName)
		{
			if (is_string($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $fieldName . ', required string, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return mixed|NULL
		 */
		private static function get(array $arr, $key)
		{
			if (array_key_exists($key, $arr)) {
				return $arr[$key];
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException("Missing field '$key'.");
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return array<string, mixed>
		 */
		private static function getArray(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if (is_array($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required array, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return string
		 */
		private static function getString(array $arr, $key)
		{
			$value = self::get($arr, $key);
			return self::string($value, $key);
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return string|NULL
		 */
		private static function getStringOrNull(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if ($value === NULL || is_string($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required scalar|NULL, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return string|string[]
		 */
		private static function getStringOrList(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if (is_string($value) || is_array($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required scalar|NULL, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return bool
		 */
		private static function getBool(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if (is_bool($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required bool, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return int
		 */
		private static function getInt(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if (is_int($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required int, ' . gettype($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed> $arr
		 * @param  string $key
		 * @return scalar|NULL
		 */
		private static function getScalarOrNull(array $arr, $key)
		{
			$value = self::get($arr, $key);

			if ($value === NULL || is_scalar($value)) {
				return $value;
			}

			throw new \Inlm\SchemaGenerator\InvalidArgumentException('Invalid field ' . $key . ', required scalar|NULL, ' . gettype($value) . ' given.');
		}
	}
