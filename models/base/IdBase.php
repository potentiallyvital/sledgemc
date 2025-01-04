<?php

class IdBase extends GettersAndSetters
{
	const CLASS_NAME = 'Id';
	const TABLE_NAME = 'id';

	var $id = null;
	var $created = null;
	var $modified = null;

	function toArray()
	{
		$array = [];
		$array['id'] = $this->id;
		$array['created'] = $this->created;
		$array['modified'] = $this->modified;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['id'] = ['bigint', 20];
		$columns['created'] = ['timestamp'];
		$columns['modified'] = ['timestamp'];

		return $columns;
	}
}