<?php

	namespace Inlm\SchemaGenerator\Bridges;

	use CzProject\SqlSchema;


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
	}
