<?php

	namespace Inlm\SchemaGenerator\Utils;


	class PhpClassParser
	{
		const FROM_LEFT = 0;
		const FROM_RIGHT = 1;

		/** @var array<array|string> */
		private $tokens;

		/** @var string|NULL */
		private $file;

		/** @var int */
		private $tokensPosition = 0;

		/** @var int */
		private $level;

		/** @var array */
		private $currentNamespace;

		/** @var int */
		private $currentLine;

		/** @var PhpClass[] */
		private $classes;


		/**
		 * @param string|NULL $file
		 */
		private function __construct(array $tokens, $file)
		{
			$this->tokens = $tokens;
			$this->file = $file;
		}


		/**
		 * @return PhpClass[]
		 */
		public function parse()
		{
			$this->tokensPosition = 0;
			$this->level = 0;
			$this->currentLine = 0;
			$this->setCurrentNamespace(NULL);
			$this->classes = [];

			while ($this->hasToken()) {
				if ($this->isCurrentToken(T_NAMESPACE)) {
					$this->parseNamespace();

				} elseif ($this->isCurrentToken(T_USE)) {
					$this->parseNamespaceUse();

				} elseif ($this->isCurrentToken(T_ABSTRACT)) {
					$this->tryParseAbstractClass();

				} elseif ($this->isCurrentToken(T_CLASS)) {
					$this->parseClass(FALSE);

				} elseif ($this->isCurrentToken(T_DOUBLE_COLON)) { // static call or property/constant
					$this->consumeToken(T_DOUBLE_COLON);
					$this->tryConsumeToken(T_CLASS);

				} elseif ($this->isCurrentToken('{') || $this->isCurrentToken(T_CURLY_OPEN) || $this->isCurrentToken(T_DOLLAR_OPEN_CURLY_BRACES)) {
					$this->level++;
					$this->nextToken();

				} elseif ($this->isCurrentToken('}')) {
					$this->level--;
					$this->nextToken();

				} else {
					$this->nextToken();
				}
			}

			$classes = $this->classes;
			$this->classes = NULL;
			return $classes;
		}


		private function parseNamespace()
		{
			$this->consumeToken(T_NAMESPACE);
			$this->consumeAllTokens(T_WHITESPACE);
			$name = NULL;

			if (!$this->isCurrentToken('{')) {
				$name = ltrim($this->parseName(), '\\');

				if ($name === '') {
					throw new \Inlm\SchemaGenerator\InvalidStateException("No namespace name defined.");
				}

				if ($this->isCurrentToken(';')) {
					$this->consumeToken(';');

				} elseif (!$this->isCurrentToken('{')) {
					throw new \Inlm\SchemaGenerator\InvalidStateException("Broken namespace definition.");
				}
			}

			$this->setCurrentNamespace($name);
		}


		private function parseNamespaceUse()
		{
			$this->consumeToken(T_USE);
			$this->consumeAllTokens(T_WHITESPACE);

			if ($this->isCurrentToken(T_FUNCTION)) { // use function
				return;
			}

			$name = NULL;

			if ($this->isCurrentToken(T_NAMESPACE)) {
				$this->consumeToken(T_NAMESPACE);
				$this->tryConsumeAllTokens(T_WHITESPACE);
				$this->consumeToken(T_NS_SEPARATOR);

				$name = ltrim($this->currentNamespace['name'] . '\\', '\\');
				$name = $name !== '' ? $name : NULL;
			}

			$name .= $this->parseName();

			if ($this->isCurrentToken('{')) { // use group
				throw new \Inlm\SchemaGenerator\IncompatibleException('Parsing of grouped USE is not implemented yet.');

			} else {
				$short = $this->generateShort($name, self::FROM_RIGHT);

				if ($this->isCurrentToken(T_AS)) {
					$this->consumeToken(T_AS);
					$this->consumeAllTokens(T_WHITESPACE);
					$short = $this->consumeToken(T_STRING);
					$this->tryConsumeAllTokens(T_WHITESPACE);
				}

				$this->consumeToken(';');
				$this->addNamespaceUse($short, $name);
			}
		}


		/**
		 * @return string
		 */
		private function parseName()
		{
			$name = '';

			if (PHP_VERSION_ID >= 80000) {
				if ($this->isCurrentToken(T_NAME_QUALIFIED)) {
					return $this->consumeToken(T_NAME_QUALIFIED);

				} elseif ($this->isCurrentToken(T_NAME_FULLY_QUALIFIED)) {
					return $this->consumeToken(T_NAME_FULLY_QUALIFIED);
				}
			}

			if ($this->isCurrentToken(T_STRING)) {
				$name .= $this->consumeToken(T_STRING);
				$this->tryConsumeAllTokens(T_WHITESPACE);
			}

			while ($this->isCurrentToken(T_NS_SEPARATOR)) {
				$name .= $this->consumeToken(T_NS_SEPARATOR);
				$this->tryConsumeAllTokens(T_WHITESPACE);

				$name .= $this->consumeToken(T_STRING);
				$this->tryConsumeAllTokens(T_WHITESPACE);
			}

			if ($name === '') {
				$line = $this->currentLine;
				$text = $this->getCurrentTokenText();
				throw new \Inlm\SchemaGenerator\InvalidStateException('Missing name' . ($line !== NULL ? " on line $line" : '') . ', token text \'' . $text . '\'.');
			}

			return $name;
		}


		private function tryParseAbstractClass()
		{
			$this->consumeToken(T_ABSTRACT);

			if ($this->isCurrentToken(T_WHITESPACE)) {
				$this->consumeAllTokens(T_WHITESPACE);

				if ($this->isCurrentToken(T_CLASS)) {
					$this->parseClass(TRUE);
				}
			}
		}


		private function parseClass($isAbstract)
		{
			$this->consumeToken(T_CLASS);
			$wasConsumed = $this->tryConsumeAllTokens(T_WHITESPACE) !== NULL;

			if ($this->isCurrentToken('{')) { // anonymouse class
				return;
			}

			if (!$wasConsumed) {
				$this->consumeAllTokens(T_WHITESPACE);
			}

			$name = ltrim($this->currentNamespace['name'] . '\\' . $this->consumeToken(T_STRING), '\\');
			$extends = NULL;
			$implements = [];

			$this->tryConsumeAllTokens(T_WHITESPACE);

			if ($this->isCurrentToken(T_EXTENDS)) {
				$this->consumeToken(T_EXTENDS);
				$this->consumeAllTokens(T_WHITESPACE);
				$extends = $this->expandName($this->parseName());

				$this->tryConsumeAllTokens(T_WHITESPACE);
			}

			if ($this->isCurrentToken(T_IMPLEMENTS)) {
				$this->consumeAllTokens(T_IMPLEMENTS);
				$this->consumeAllTokens(T_WHITESPACE);
				$implements[] = $this->expandName($this->parseName());

				while ($this->isCurrentToken(',')) {
					$this->consumeToken(',');
					$this->tryConsumeAllTokens(T_WHITESPACE);
					$implements[] = $this->expandName($this->parseName());
					$this->tryConsumeAllTokens(T_WHITESPACE);
				}
			}

			$this->classes[] = new PhpClass($name, $isAbstract, $extends, $implements, $this->file);
			$this->parsePhpBlock();
		}


		private function parsePhpBlock()
		{
			$startLevel = $this->level;
			$this->consumeToken('{');
			$this->level++;

			while ($this->hasToken()) {
				if ($this->isCurrentToken('{')) {
					$this->level++;

				} elseif ($this->isCurrentToken('}')) {
					$this->level--;

					if ($this->level === $startLevel) {
						$this->consumeToken('}');
						return;
					}

				} elseif ($this->isCurrentToken(T_CURLY_OPEN) || $this->isCurrentToken(T_DOLLAR_OPEN_CURLY_BRACES)) {
					$this->level++;
				}

				$this->nextToken();
			}

			throw new \Inlm\SchemaGenerator\InvalidStateException('Invalid block level.');
		}


		/**
		 * @param  int|string $id
		 * @return string
		 */
		private function consumeToken($id)
		{
			if (!$this->isCurrentToken($id)) {
				$currentToken = $this->getCurrentTokenId();
				$currentTokenText = is_int($currentToken) ? (' (text: ' . $this->getCurrentTokenText() . ')') : '';
				$currentTokenLine = is_int($currentToken) ? (' on line' . $this->getCurrentTokenLine()) : '';
				$currentToken = is_int($currentToken) ? token_name($currentToken) : $currentToken;
				$expectedToken = is_int($id) ? token_name($id) : $id;
				throw new \Inlm\SchemaGenerator\InvalidStateException("Invalid token '{$currentToken}'{$currentTokenText}{$currentTokenLine}, expected '{$expectedToken}'.");
			}

			$text = $this->getCurrentTokenText();
			$this->nextToken();
			return $text;
		}


		/**
		 * @param  int|string $id
		 * @return string|NULL
		 */
		private function tryConsumeToken($id)
		{
			if ($this->isCurrentToken($id)) {
				return $this->consumeToken($id);
			}

			return NULL;
		}


		/**
		 * @param  int|string $id
		 * @return string
		 */
		private function consumeAllTokens($id)
		{
			$text = $this->consumeToken($id);

			while ($this->isCurrentToken($id)) {
				$text .= $this->consumeToken($id);
			}

			return $text;
		}


		/**
		 * @param  int|string $id
		 * @return string|NULL
		 */
		private function tryConsumeAllTokens($id)
		{
			if ($this->isCurrentToken($id)) {
				return $this->consumeAllTokens($id);
			}

			return NULL;
		}


		/**
		 * @return bool
		 */
		private function hasToken()
		{
			return isset($this->tokens[$this->tokensPosition]);
		}


		/**
		 * @param  string|int $tokenId
		 * @return bool
		 */
		private function isCurrentToken($tokenId)
		{
			$token = $this->getCurrentToken();

			if (is_string($token) && $token === $tokenId) {
				return TRUE;
			}

			if (is_array($token) && $token[0] === $tokenId) {
				return TRUE;
			}

			return FALSE;
		}


		/**
		 * @return array|string
		 */
		private function getCurrentToken()
		{
			if (!isset($this->tokens[$this->tokensPosition])) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('Missing token at position ' . $this->tokensPosition . '.');
			}

			return $this->tokens[$this->tokensPosition];
		}


		/**
		 * @return int|string
		 */
		private function getCurrentTokenId()
		{
			$token = $this->getCurrentToken();
			return is_array($token) ? $token[0] : $token;
		}


		/**
		 * @return string
		 */
		private function getCurrentTokenText()
		{
			$token = $this->getCurrentToken();
			return is_array($token) ? $token[1] : $token;
		}


		/**
		 * @return int|NULL
		 */
		private function getCurrentTokenLine()
		{
			$token = $this->getCurrentToken();
			return is_array($token) ? $token[2] : NULL;
		}


		/**
		 * @return void
		 */
		private function nextToken()
		{
			if (($this->tokensPosition + 1) > count($this->tokens)) {
				throw new \Inlm\SchemaGenerator\InvalidStateException('There no next position.');
			}

			$this->tokensPosition++;

			if (isset($this->tokens[$this->tokensPosition]) && is_array($this->tokens[$this->tokensPosition])) {
				$this->currentLine = $this->tokens[$this->tokensPosition][2];
			}
		}


		/**
		 * @param  string|NULL $name
		 * @return void
		 */
		private function setCurrentNamespace($name)
		{
			$this->currentNamespace = [
				'name' => $name,
				'uses' => [],
			];
		}


		/**
		 * @param  string $alias
		 * @param  string $name
		 * @return void
		 */
		private function addNamespaceUse($alias, $name)
		{
			$this->currentNamespace['uses'][$alias] = $name;
		}


		/**
		 * @param  string $name
		 * @return string
		 */
		private function expandName($name)
		{
			if ($name[0] === '\\' || !$name) {
				return substr($name, 1);

			} else {
				$short = $this->generateShort($name, self::FROM_LEFT);

				if (isset($this->currentNamespace['uses'][$short])) {
					if ($short === $name) {
						return $this->currentNamespace['uses'][$short];
					}

					return $this->currentNamespace['uses'][$short] . '\\' . substr($name, strlen($short) + 1);
				}
			}

			return ltrim($this->currentNamespace['name'] . '\\' . $name, '\\');
		}


		/**
		 * @param  string $name
		 * @param  int $fromRight
		 * @return string
		 */
		private function generateShort($name, $direction)
		{
			$short = trim($name, '\\');
			$pos = $direction === self::FROM_RIGHT ? strrpos($short, '\\') : strpos($short, '\\');

			if ($pos !== FALSE) {
				if ($direction === self::FROM_RIGHT) {
					$short = substr($short, $pos + 1);

				} else {
					$short = substr($short, 0, $pos);
				}
			}

			return $short;
		}


		/**
		 * @param  string $file
		 * @return self
		 */
		public static function fromFile($file)
		{
			return new self(token_get_all(file_get_contents($file)), $file);
		}


		/**
		 * @param  string $s
		 * @return self
		 */
		public static function fromString($s)
		{
			return new self(token_get_all($s), NULL);
		}
	}
