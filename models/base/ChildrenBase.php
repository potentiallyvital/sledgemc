<?php

class ChildrenBase extends Id
{
	const CLASS_NAME = 'Children';
	const TABLE_NAME = 'children';

	var $parent_id = null;
	var $parent_table = null;
	var $parent_class = null;
	var $child_id = null;
	var $child_table = null;
	var $child_class = null;

	function toArray()
	{
		$array = [];
		$array['parent_id'] = $this->parent_id;
		$array['parent_table'] = $this->parent_table;
		$array['parent_class'] = $this->parent_class;
		$array['child_id'] = $this->child_id;
		$array['child_table'] = $this->child_table;
		$array['child_class'] = $this->child_class;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['parent_id'] = ['bigint', 20];
		$columns['parent_table'] = ['character varying', 255];
		$columns['parent_class'] = ['character varying', 255];
		$columns['child_id'] = ['bigint', 20];
		$columns['child_table'] = ['character varying', 255];
		$columns['child_class'] = ['character varying', 255];

		return $columns;
	}
}