<?php

	namespace Inlm\SchemaGenerator\Diffs;


	class UpdatedTableComment
	{
		/** @var string */
		private $tableName;

		/** @var string */
		private $comment;


		/**
		 * @param string $tableName
		 * @param string $comment
		 */
		public function __construct($tableName, $comment)
		{
			$this->tableName = $tableName;
			$this->comment = $comment;
		}


		/**
		 * @return string
		 */
		public function getTableName()
		{
			return $this->tableName;
		}


		/**
		 * @return string
		 */
		public function getComment()
		{
			return $this->comment;
		}
	}
