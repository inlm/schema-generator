<?php

	declare(strict_types=1);

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
		 * @param  string|NULL $inputType
		 * @param  DataType|NULL $dbType
		 * @param  bool $isPrimaryColumn
		 * @param  array<lowercase-string, DataType> $customTypes
		 * @param  string|NULL $databaseType
		 * @return DataType
		 */
		public static function process($inputType, ?DataType $dbType = NULL, $isPrimaryColumn = FALSE, array $customTypes = [], $databaseType = NULL)
		{
			$type = NULL;
			$parameters = [];
			$options = [];

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
					$parameters = [1];
					$options[] = SqlSchema\Column::OPTION_UNSIGNED;

				} elseif ($inputType === 'float' || $inputType === 'double') {
					$type = 'DOUBLE';

				} elseif ($inputType === 'string') {
					$type = 'TEXT';

				} elseif ($inputType === 'datetime' || $inputType === 'datetimeinterface' || $inputType === 'datetimeimmutable') {
					$type = 'DATETIME';
				}
			}

			if ($dbType !== NULL) {
				if ($dbType->getType() !== NULL) {
					$type = $dbType->getType();
					$lowerType = strtolower($type);

					if (isset($customTypes[$lowerType])) {
						$columnType = $customTypes[$lowerType];
						$type = $columnType->getType();
						$parameters = $columnType->getParameters();
						$options = $columnType->getOptions();
					}
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
				throw new MissingException('Missing type' . ($inputType !== NULL ? " for [$inputType]" : '') . '.');
			}

			return new DataType($type, $parameters, $options);
		}
	}
