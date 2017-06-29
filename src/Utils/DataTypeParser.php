<?php

	namespace Inlm\SchemaGenerator\Utils;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\DataType;
	use Inlm\SchemaGenerator\DuplicatedException;
	use Inlm\SchemaGenerator\InvalidArgumentException;
	use Inlm\SchemaGenerator\MissingException;
	use Inlm\SchemaGenerator\StaticClassException;


	class DataTypeParser
	{
		const SYNTAX_DEFAULT = 0;
		const SYNTAX_ALTERNATIVE = 1;

		const PARSER_CONTEXT = 0;
		const PARSER_TOKEN = 1;

		const CONTEXT_DEFAULT = 0;
		const CONTEXT_ARGS = 1;
		const CONTEXT_ALT_ARGS = 2;
		const CONTEXT_FINISH = 3;

		const TOKEN_DEFAULT = 0;
		const TOKEN_ARGUMENT = 1;


		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @param  string
		 * @param  int
		 * @return DataType
		 */
		public static function parse($s, $syntax = self::SYNTAX_DEFAULT)
		{
			$type = NULL;
			$parameters = array();
			$options = array();
			$delimiters = array(
				self::CONTEXT_DEFAULT => array(
					// tokenType, newContext
					' ' => array(self::TOKEN_DEFAULT, self::CONTEXT_DEFAULT),
					"\t" => array(self::TOKEN_DEFAULT, self::CONTEXT_DEFAULT),
					"\n" => array(self::TOKEN_DEFAULT, self::CONTEXT_DEFAULT),
					"\r" => array(self::TOKEN_DEFAULT, self::CONTEXT_DEFAULT),
					'(' => array(self::TOKEN_DEFAULT, self::CONTEXT_ARGS),
				),
				self::CONTEXT_ARGS => array(
					',' => array(self::TOKEN_ARGUMENT, self::CONTEXT_ARGS),
					')' => array(self::TOKEN_ARGUMENT, self::CONTEXT_DEFAULT),
				),
				self::CONTEXT_FINISH => array(
					self::CONTEXT_DEFAULT => self::TOKEN_DEFAULT,
					self::CONTEXT_ARGS => self::TOKEN_ARGUMENT,
				),
			);

			if ($syntax & self::SYNTAX_ALTERNATIVE) {
				$delimiters[self::CONTEXT_DEFAULT][':'] = array(self::TOKEN_DEFAULT, self::CONTEXT_ALT_ARGS);
				$delimiters[self::CONTEXT_ALT_ARGS] = array(
					',' => array(self::TOKEN_ARGUMENT, self::CONTEXT_ALT_ARGS),
					' ' => array(self::TOKEN_ARGUMENT, self::CONTEXT_DEFAULT),
					"\t" => array(self::TOKEN_ARGUMENT, self::CONTEXT_DEFAULT),
					"\n" => array(self::TOKEN_ARGUMENT, self::CONTEXT_DEFAULT),
					"\r" => array(self::TOKEN_ARGUMENT, self::CONTEXT_DEFAULT),
				);
				$delimiters[self::CONTEXT_FINISH][self::CONTEXT_ALT_ARGS] = self::TOKEN_ARGUMENT;
			}

			$findParameters = TRUE;
			$inParameters = FALSE;
			$tokens = self::process($s, self::CONTEXT_DEFAULT, $delimiters);
			$knownOptions = array(
				SqlSchema\Column::OPTION_UNSIGNED => TRUE,
				SqlSchema\Column::OPTION_ZEROFILL => TRUE,
				SqlSchema\Column::OPTION_BINARY => TRUE,
			);

			foreach ($tokens as $token) {
				if ($token[0] === self::TOKEN_ARGUMENT) {
					if (!$findParameters) {
						throw new InvalidArgumentException('Malformed parameters definition.');
					}

					$inParameters = TRUE;
					$value = trim($token[1]);
					$intValue = (int) $value;

					if ($value !== (string) $intValue) {
						throw new InvalidArgumentException("Value must be integer, '$value' given.");
					}

					$parameters[] = $intValue;

				} else {
					if ($inParameters) {
						$inParameters = FALSE;
						$findParameters = FALSE;
					}

					$upperToken = strtoupper($token[1]);

					if (isset($knownOptions[$upperToken])) {
						$options[] = $upperToken;
						continue;
					}

					if ($type === NULL) {
						$type = $upperToken;

					} else {
						$options[] = $upperToken;
					}
				}
			}

			return new DataType(
				$type,
				$parameters,
				$options
			);
		}


		private static function process($s, $context, array $delimiters)
		{
			$result = array();
			$pos = 0;
			$length = strlen($s);

			for ($i = 0; $i < $length; $i++) {
				$char = $s[$i];

				if (isset($delimiters[$context][$char])) { // fetch token
					$token = substr($s, $pos, $i - $pos);
					$pos = $i + 1;
					$action = $delimiters[$context][$char];

					if (is_array($action)) {
						if ($token !== '') {
							$result[] = array($action[0], $token);
						}

						$context = $action[1];
					}
				}
			}

			if ($pos !== $i && isset($delimiters[self::CONTEXT_FINISH][$context])) {
				$token = substr($s, $pos, $i - $pos);

				if ($token !== '') {
					$result[] = array($delimiters[self::CONTEXT_FINISH][$context], $token);
				}
			}

			return $result;
		}
	}
