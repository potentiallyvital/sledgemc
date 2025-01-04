<?php

class AccountBase extends Id
{
	const CLASS_NAME = 'Account';
	const TABLE_NAME = 'account';

	var $role_id = null;
	var $first_name = null;
	var $last_name = null;
	var $email = null;
	var $phone = null;
	var $street = null;
	var $unit = null;
	var $city = null;
	var $state = null;
	var $zip = null;
	var $zip_4 = null;

	function toArray()
	{
		$array = [];
		$array['role_id'] = $this->role_id;
		$array['first_name'] = $this->first_name;
		$array['last_name'] = $this->last_name;
		$array['email'] = $this->email;
		$array['phone'] = $this->phone;
		$array['street'] = $this->street;
		$array['unit'] = $this->unit;
		$array['city'] = $this->city;
		$array['state'] = $this->state;
		$array['zip'] = $this->zip;
		$array['zip_4'] = $this->zip_4;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['role_id'] = ['bigint', 20];
		$columns['first_name'] = ['character varying', 64];
		$columns['last_name'] = ['character varying', 64];
		$columns['email'] = ['character varying', 64];
		$columns['phone'] = ['character varying', 10];
		$columns['street'] = ['character varying', 64];
		$columns['unit'] = ['character varying', 64];
		$columns['city'] = ['character varying', 64];
		$columns['state'] = ['character varying', 2];
		$columns['zip'] = ['character varying', 5];
		$columns['zip_4'] = ['character varying', 4];

		return $columns;
	}
}