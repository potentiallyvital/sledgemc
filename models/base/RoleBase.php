<?php

class RoleBase extends Id
{
	const CLASS_NAME = 'Role';
	const TABLE_NAME = 'role';

	var $name = null;
	var $code = null;

	function toArray()
	{
		$array = [];
		$array['name'] = $this->name;
		$array['code'] = $this->code;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['name'] = ['character varying', 64];
		$columns['code'] = ['character varying', 64];

		return $columns;
	}
}