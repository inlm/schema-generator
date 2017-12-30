<?php

	namespace Inlm\SchemaGenerator\Bridges;

	use CzProject\SqlSchema;


	class Dibi
	{
		/**
		 * @param  object
		 * @return void
		 * @throws \Inlm\SchemaGenerator\InvalidArgumentException
		 */
		public static function validateConnection($connection)
		{
			if (!($connection instanceof \Dibi\Connection || $connection instanceof \DibiConnection)) {
				throw new \Inlm\SchemaGenerator\InvalidArgumentException('Connection must be instance of Dibi\Connection or DibiConnection.');
			}
		}


		/**
		 * @param  object
		 * @return bool
		 */
		public static function isMysqlDriver($driver)
		{
			return $driver instanceof \Dibi\Drivers\MySqlDriver
				|| $driver instanceof \DibiMySqlDriver
				|| $driver instanceof \Dibi\Drivers\MySqliDriver
				|| $driver instanceof \DibiMySqliDriver;
		}
	}
