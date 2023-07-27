<?php

	namespace Inlm\SchemaGenerator;


	class ConfigurationSerializer
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @return array<string, mixed>
		 */
		public static function serialize(Configuration $configuration)
		{
			$res = [];
			$options = $configuration->getOptions();

			if (!empty($options)) {
				ksort($options, SORT_STRING);
				$res['options'] = $options;
			}

			$schema = $configuration->getSchema();
			$tables = [];
			$_foreignKeys = [];

			foreach ($schema->getTables() as $table) {
				$table->validate();
				$tableName = $table->getName();

				if (isset($tables[$tableName])) {
					throw new DuplicatedException("Duplicated table name '$tableName'.");
				}

				$definition = self::export([
					'comment' => $table->getComment(),
				], ['comment' => NULL]);

				foreach ($table->getColumns() as $column) {
					$columnName = $column->getName();

					if (!isset($definition['columns'])) {
						$definition['columns'] = [];
					}

					if (!is_array($definition['columns'])) {
						throw new \Inlm\SchemaGenerator\InvalidStateException('Definition must be array.');
					}

					$definition['columns'][$columnName] = self::export([
						'type' => $column->getType(),
						'parameters' => $column->getParameters(),
						'options' => $column->getOptions(),
						'nullable' => $column->isNullable(),
						'autoIncrement' => $column->isAutoIncrement(),
						'defaultValue' => $column->getDefaultValue(),
						'comment' => $column->getComment(),
					], [
						'parameters' => [],
						'options' => [],
						'nullable' => FALSE,
						'autoIncrement' => FALSE,
						'defaultValue' => NULL,
						'comment' => NULL,
					]);
				}

				foreach ($table->getIndexes() as $index) {
					$indexName = $index->getName();
					$indexColumns = [];

					foreach ($index->getColumns() as $indexColumn) {
						$indexColumns[] = self::export([
							'name' => $indexColumn->getName(),
							'order' => $indexColumn->getOrder(),
							'length' => $indexColumn->getLength(),
						], [
							'order' => 'ASC',
							'length' => NULL,
						]);
					}

					if (!isset($definition['indexes'])) {
						$definition['indexes'] = [];
					}

					if (!is_array($definition['indexes'])) {
						throw new \Inlm\SchemaGenerator\InvalidStateException('Definition must be array.');
					}

					$definition['indexes'][$indexName] = [
						'type' => $index->getType(),
						'columns' => $indexColumns,
					];
				}

				foreach ($table->getForeignKeys() as $foreignKey) {
					$foreignKeyName = $foreignKey->getName();

					if (isset($_foreignKeys[$foreignKeyName])) {
						throw new DuplicatedException("Duplicated foreign key '$foreignKeyName' in table '$tableName'.");
					}

					if (!isset($definition['foreignKeys'])) {
						$definition['foreignKeys'] = [];
					}

					if (!is_array($definition['foreignKeys'])) {
						throw new \Inlm\SchemaGenerator\InvalidStateException('Definition must be array.');
					}

					$definition['foreignKeys'][$foreignKeyName] = [
						'columns' => $foreignKey->getColumns(),
						'targetTable' => $foreignKey->getTargetTable(),
						'targetColumns' => $foreignKey->getTargetColumns(),
						'onUpdateAction' => $foreignKey->getOnUpdateAction(),
						'onDeleteAction' => $foreignKey->getOnDeleteAction(),
					];
					$_foreignKeys[$foreignKeyName] = TRUE;
				}


				foreach ($table->getOptions() as $optionName => $optionValue) {
					if (!isset($definition['options'])) {
						$definition['options'] = [];
					}

					if (!is_array($definition['options'])) {
						throw new \Inlm\SchemaGenerator\InvalidStateException('Definition must be array.');
					}

					$definition['options'][$optionName] = $optionValue;
				}

				isset($definition['indexes']) && is_array($definition['indexes']) && ksort($definition['indexes'], SORT_STRING);
				isset($definition['foreignKeys']) && is_array($definition['foreignKeys']) && ksort($definition['foreignKeys'], SORT_STRING);
				isset($definition['options']) && is_array($definition['options']) && ksort($definition['options'], SORT_STRING);

				$tables[$tableName] = $definition;
			}

			if (!empty($tables)) {
				ksort($tables, SORT_STRING);
				$res['schema'] = $tables;
			}

			return $res;
		}


		/**
		 * @param  array<string, mixed> $data
		 * @param  array<string, mixed> $defaults
		 * @return array<string, mixed>
		 */
		private static function export(array $data, array $defaults = [])
		{
			foreach ($defaults as $key => $value) {
				if ($data[$key] === $value) {
					unset($data[$key]);
				}
			}

			return $data;
		}
	}
