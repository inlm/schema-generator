<?php

	namespace Inlm\SchemaGenerator\Bridges;

	use CzProject\SqlSchema;
	use Inlm\SchemaGenerator\Database;


	class Dibi
	{
		/**
		 * @param  object
		 * @return bool
		 */
		public static function isMysqlDriver($driver)
		{
			return $driver instanceof \Dibi\Drivers\MySqlDriver
				|| $driver instanceof \Dibi\Drivers\MySqliDriver;
		}


		/**
		 * @return string
		 * @throws \Inlm\SchemaGenerator\UnsupportedException
		 */
		public static function detectDatabaseType(\Dibi\Connection $connection)
		{
			$driver = $connection->getDriver();

			if (self::isMysqlDriver($driver)) {
				return Database::MYSQL;
			}

			throw new \Inlm\SchemaGenerator\UnsupportedException('Unsupported driver type ' . get_class($driver));
		}
	}
