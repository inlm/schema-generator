<?php

	namespace Inlm\SchemaGenerator\Loggers;

	use Inlm\SchemaGenerator\ILogger;


	class OutputLogger implements ILogger
	{
		/**
		 * @param  string
		 * @return void
		 */
		public function log($msg)
		{
			echo $msg, "\n";
		}
	}
