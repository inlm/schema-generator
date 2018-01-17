<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\DataType;
	use Inlm\SchemaGenerator\DuplicatedException;
	use Inlm\SchemaGenerator\InvalidArgumentException;
	use Inlm\SchemaGenerator\MissingException;
	use Inlm\SchemaGenerator\StaticClassException;


	class DataTypeProcessor
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @param  string|NULL
		 * @param  DataType|NULL
		 * @param  bool
		 * @param  array  [type => DataType]
		 * @param  string|NULL
		 * @return DataType
		 */
		public static function process($inputType, DataType $dbType = NULL, $isPrimaryColumn, array $customTypes, $databaseType = NULL)
		{
			$type = NULL;
			$parameters = array();
			$options = array();

			if ($inputType !== NULL) {
				// predefined & custom types
				$inputType = strtolower($inputType);

				if (isset($customTypes[$inputType])) {
					$columnType = $customTypes[$inputType];
					$type = $columnType->getType();
					$parameters = $columnType->getParameters();
					$options = $columnType->getOptions();

				} elseif ($inputType === 'integer' || $inputType === 'int') {
					$type = 'INT';

					if ($isPrimaryColumn) {
						$options[] = SqlSchema\Column::OPTION_UNSIGNED;
					}

				} elseif ($inputType === 'boolean' || $inputType === 'bool') {
					$type = 'TINYINT';
					$parameters = array(1);
					$options[] = 'UNSIGNED';

				} elseif ($inputType === 'float' || $inputType === 'double') {
					$type = 'DOUBLE';

				} elseif ($inputType === 'string') {
					$type = 'TEXT';

				} elseif ($inputType === 'datetime' || $inputType === 'datetimeinterface') {
					$type = 'DATETIME';
				}
			}

			if ($dbType !== NULL) {
				if ($dbType->getType() !== NULL) {
					$type = $dbType->getType();
				}

				if ($dbType->getParameters() !== NULL) {
					$parameters = $dbType->getParameters();
				}

				foreach ($dbType->getOptions() as $k => $v) {
					if (is_int($k)) {
						$options[$v] = NULL;

					} else {
						$options[$k] = $v;
					}
				}
			}

			if ($type === NULL) {
				throw new MissingException('Missing type.');
			}

			return new DataType($type, $parameters, $options);
		}
	}
