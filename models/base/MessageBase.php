<?php

class MessageBase extends Id
{
	const CLASS_NAME = 'Message';
	const TABLE_NAME = 'message';

	var $subject = null;
	var $body = null;

	function toArray()
	{
		$array = [];
		$array['subject'] = $this->subject;
		$array['body'] = $this->body;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['subject'] = ['character varying', 255];
		$columns['body'] = ['text'];

		return $columns;
	}
}