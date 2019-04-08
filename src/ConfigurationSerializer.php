<?php

	namespace Inlm\SchemaGenerator;


	class ConfigurationSerializer
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @return array
		 */
		public static function serialize(Configuration $configuration)
		{
			$res = array();
			$options = $configuration->getOptions();

			if (!empty($options)) {
				ksort($options, SORT_STRING);
				$res['options'] = $options;
			}

			$schema = $configuration->getSchema();
			$tables = array();
			$_foreignKeys = array();

			foreach ($schema->getTables() as $table) {
				$table->validate();
				$tableName = $table->getName();

				if (isset($tables[$tableName])) {
					throw new DuplicatedException("Duplicated table name '$tableName'.");
				}

				$definition = self::export(array(
					'comment' => $table->getComment(),
				), array('comment' => NULL));

				foreach ($table->getColumns() as $column) {
					$columnName = $column->getName();

					$definition['columns'][$columnName] = self::export(array(
						'type' => $column->getType(),
						'parameters' => $column->getParameters(),
						'options' => $column->getOptions(),
						'nullable' => $column->isNullable(),
						'autoIncrement' => $column->isAutoIncrement(),
						'defaultValue' => $column->getDefaultValue(),
						'comment' => $column->getComment(),
					), array(
						'parameters' => array(),
						'options' => array(),
						'nullable' => FALSE,
						'autoIncrement' => FALSE,
						'defaultValue' => NULL,
						'comment' => NULL,
					));
				}

				foreach ($table->getIndexes() as $index) {
					$indexName = $index->getName();
					$indexColumns = array();

					foreach ($index->getColumns() as $indexColumn) {
						$indexColumns[] = self::export(array(
							'name' => $indexColumn->getName(),
							'order' => $indexColumn->getOrder(),
							'length' => $indexColumn->getLength(),
						), array(
							'order' => 'ASC',
							'length' => NULL,
						));
					}

					$definition['indexes'][$indexName] = array(
						'type' => $index->getType(),
						'columns' => $indexColumns,
					);
				}

				foreach ($table->getForeignKeys() as $foreignKey) {
					$foreignKeyName = $foreignKey->getName();

					if (isset($_foreignKeys[$foreignKeyName])) {
						throw new DuplicatedException("Duplicated foreign key '$foreignKeyName' in table '$tableName'.");
					}

					$definition['foreignKeys'][$foreignKeyName] = array(
						'columns' => $foreignKey->getColumns(),
						'targetTable' => $foreignKey->getTargetTable(),
						'targetColumns' => $foreignKey->getTargetColumns(),
						'onUpdateAction' => $foreignKey->getOnUpdateAction(),
						'onDeleteAction' => $foreignKey->getOnDeleteAction(),
					);
					$_foreignKeys[$foreignKeyName] = TRUE;
				}


				foreach ($table->getOptions() as $optionName => $optionValue) {
					$definition['options'][$optionName] = $optionValue;
				}

				isset($definition['indexes']) && ksort($definition['indexes'], SORT_STRING);
				isset($definition['foreignKeys']) && ksort($definition['foreignKeys'], SORT_STRING);
				isset($definition['options']) && ksort($definition['options'], SORT_STRING);

				$tables[$tableName] = $definition;
			}

			if (!empty($tables)) {
				ksort($tables, SORT_STRING);
				$res['schema'] = $tables;
			}

			return $res;
		}


		private static function export(array $data, array $defaults = array())
		{
			foreach ($defaults as $key => $value) {
				if ($data[$key] === $value) {
					unset($data[$key]);
				}
			}

			return $data;
		}
	}
