<?php

class LoginBase extends Id
{
	const CLASS_NAME = 'Login';
	const TABLE_NAME = 'login';

	var $email = null;
	var $password = null;
	var $invalid_logins = null;
	var $temporary_pass = null;
	var $temporary_expires = null;

	function toArray()
	{
		$array = [];
		$array['email'] = $this->email;
		$array['password'] = $this->password;
		$array['invalid_logins'] = $this->invalid_logins;
		$array['temporary_pass'] = $this->temporary_pass;
		$array['temporary_expires'] = $this->temporary_expires;

		foreach (parent::toArray() as $key => $value)
		{
			$array[$key] = $value;
		}

		return $array;
	}

	function getColumnTypes()
	{
		$columns = parent::getColumnTypes();

		$columns['email'] = ['character varying', 64];
		$columns['password'] = ['text'];
		$columns['invalid_logins'] = ['bigint', 20];
		$columns['temporary_pass'] = ['character varying', 64];
		$columns['temporary_expires'] = ['timestamp'];

		return $columns;
	}
}