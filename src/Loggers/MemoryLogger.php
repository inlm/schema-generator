<?php

	namespace Inlm\SchemaGenerator\Loggers;

	use Inlm\SchemaGenerator\ILogger;


	class MemoryLogger implements ILogger
	{
		/** @var string */
		private $log = '';


		/**
		 * @param  string
		 * @return void
		 */
		public function log($msg, $level = self::INFO)
		{
			$this->log .= $msg;
			$this->log .= "\n";
		}


		/**
		 * @return string
		 */
		public function getLog()
		{
			return $this->log;
		}
	}
