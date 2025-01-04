<?php

class GettersAndSetters extends BaseObject
{
	function setBody($value = null)
	{
		return self::set($this, 'body', $value);
	}

	function getBody($vars = null)
	{
		if (!property_exists($this, 'body'))
		{
			return null;
		}
		return $this->sanitize('body', $this->body);
	}

	static function getByBody($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE body = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setChildClass($value = null)
	{
		return self::set($this, 'child_class', $value);
	}

	function getChildClass($vars = null)
	{
		if (!property_exists($this, 'child_class'))
		{
			return null;
		}
		return $this->sanitize('child_class', $this->child_class);
	}

	static function getByChildClass($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE child_class = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setChildId($value = null)
	{
		return self::set($this, 'child_id', $value);
	}

	function getChildId($vars = null)
	{
		if (!property_exists($this, 'child_id'))
		{
			return null;
		}
		$value = $this->sanitize('child_id', $this->child_id);
		if ($vars)
		{
			$value = number_format($this->child_id);
		}
		return $value;
	}

	static function getByChildId($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE child_id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setChildTable($value = null)
	{
		return self::set($this, 'child_table', $value);
	}

	function getChildTable($vars = null)
	{
		if (!property_exists($this, 'child_table'))
		{
			return null;
		}
		return $this->sanitize('child_table', $this->child_table);
	}

	static function getByChildTable($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE child_table = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setCity($value = null)
	{
		return self::set($this, 'city', $value);
	}

	function getCity($vars = null)
	{
		if (!property_exists($this, 'city'))
		{
			return null;
		}
		return $this->sanitize('city', $this->city);
	}

	static function getByCity($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE city = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setCode($value = null)
	{
		return self::set($this, 'code', $value);
	}

	function getCode($vars = null)
	{
		if (!property_exists($this, 'code'))
		{
			return null;
		}
		return $this->sanitize('code', $this->code);
	}

	static function getByCode($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE code = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setCreated($value = null)
	{
		return self::set($this, 'created', $value);
	}

	function getCreated($vars = null)
	{
		if (!property_exists($this, 'created'))
		{
			return null;
		}
		if ($vars == '')
		{
			$vars = 'Y-m-d H:i:s';
		}
		return $this->formatTime($this->created, $vars);
	}

	static function getByCreated($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE created = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setEmail($value = null)
	{
		return self::set($this, 'email', $value);
	}

	function getEmail($vars = null)
	{
		if (!property_exists($this, 'email'))
		{
			return null;
		}
		return $this->sanitize('email', $this->email);
	}

	static function getByEmail($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE email = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setFirstName($value = null)
	{
		return self::set($this, 'first_name', $value);
	}

	function getFirstName($vars = null)
	{
		if (!property_exists($this, 'first_name'))
		{
			return null;
		}
		return $this->sanitize('first_name', $this->first_name);
	}

	static function getByFirstName($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE first_name = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setId($value = null)
	{
		return self::set($this, 'id', $value);
	}

	function getId($vars = null)
	{
		if (!property_exists($this, 'id'))
		{
			return null;
		}
		$value = $this->sanitize('id', $this->id);
		if ($vars)
		{
			$value = number_format($this->id);
		}
		return $value;
	}

	static function getById($value, $first_only = true, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setInvalidLogins($value = null)
	{
		return self::set($this, 'invalid_logins', $value);
	}

	function getInvalidLogins($vars = null)
	{
		if (!property_exists($this, 'invalid_logins'))
		{
			return null;
		}
		$value = $this->sanitize('invalid_logins', $this->invalid_logins);
		if ($vars)
		{
			$value = number_format($this->invalid_logins);
		}
		return $value;
	}

	static function getByInvalidLogins($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE invalid_logins = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setLastName($value = null)
	{
		return self::set($this, 'last_name', $value);
	}

	function getLastName($vars = null)
	{
		if (!property_exists($this, 'last_name'))
		{
			return null;
		}
		return $this->sanitize('last_name', $this->last_name);
	}

	static function getByLastName($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE last_name = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setModified($value = null)
	{
		return self::set($this, 'modified', $value);
	}

	function getModified($vars = null)
	{
		if (!property_exists($this, 'modified'))
		{
			return null;
		}
		if ($vars == '')
		{
			$vars = 'Y-m-d H:i:s';
		}
		return $this->formatTime($this->modified, $vars);
	}

	static function getByModified($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE modified = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setName($value = null)
	{
		return self::set($this, 'name', $value);
	}

	function getName($vars = null)
	{
		if (!property_exists($this, 'name'))
		{
			return null;
		}
		return $this->sanitize('name', $this->name);
	}

	static function getByName($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE name = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParentClass($value = null)
	{
		return self::set($this, 'parent_class', $value);
	}

	function getParentClass($vars = null)
	{
		if (!property_exists($this, 'parent_class'))
		{
			return null;
		}
		return $this->sanitize('parent_class', $this->parent_class);
	}

	static function getByParentClass($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE parent_class = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParentId($value = null)
	{
		return self::set($this, 'parent_id', $value);
	}

	function getParentId($vars = null)
	{
		if (!property_exists($this, 'parent_id'))
		{
			return null;
		}
		$value = $this->sanitize('parent_id', $this->parent_id);
		if ($vars)
		{
			$value = number_format($this->parent_id);
		}
		return $value;
	}

	static function getByParentId($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE parent_id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParentTable($value = null)
	{
		return self::set($this, 'parent_table', $value);
	}

	function getParentTable($vars = null)
	{
		if (!property_exists($this, 'parent_table'))
		{
			return null;
		}
		return $this->sanitize('parent_table', $this->parent_table);
	}

	static function getByParentTable($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE parent_table = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setPassword($value = null)
	{
		return self::set($this, 'password', $value);
	}

	function getPassword($vars = null)
	{
		if (!property_exists($this, 'password'))
		{
			return null;
		}
		return $this->sanitize('password', $this->password);
	}

	static function getByPassword($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE password = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setPhone($value = null)
	{
		return self::set($this, 'phone', $value);
	}

	function getPhone($vars = null)
	{
		if (!property_exists($this, 'phone'))
		{
			return null;
		}
		return $this->sanitize('phone', $this->phone);
	}

	static function getByPhone($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE phone = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setRoleId($value = null)
	{
		return self::set($this, 'role_id', $value);
	}

	function getRoleId($vars = null)
	{
		if (!property_exists($this, 'role_id'))
		{
			return null;
		}
		$value = $this->sanitize('role_id', $this->role_id);
		if ($vars)
		{
			$value = number_format($this->role_id);
		}
		return $value;
	}

	static function getByRoleId($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE role_id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setState($value = null)
	{
		return self::set($this, 'state', $value);
	}

	function getState($vars = null)
	{
		if (!property_exists($this, 'state'))
		{
			return null;
		}
		return $this->sanitize('state', $this->state);
	}

	static function getByState($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE state = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setStreet($value = null)
	{
		return self::set($this, 'street', $value);
	}

	function getStreet($vars = null)
	{
		if (!property_exists($this, 'street'))
		{
			return null;
		}
		return $this->sanitize('street', $this->street);
	}

	static function getByStreet($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE street = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setSubject($value = null)
	{
		return self::set($this, 'subject', $value);
	}

	function getSubject($vars = null)
	{
		if (!property_exists($this, 'subject'))
		{
			return null;
		}
		return $this->sanitize('subject', $this->subject);
	}

	static function getBySubject($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE subject = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setTemporaryExpires($value = null)
	{
		return self::set($this, 'temporary_expires', $value);
	}

	function getTemporaryExpires($vars = null)
	{
		if (!property_exists($this, 'temporary_expires'))
		{
			return null;
		}
		if ($vars == '')
		{
			$vars = 'Y-m-d H:i:s';
		}
		return $this->formatTime($this->temporary_expires, $vars);
	}

	static function getByTemporaryExpires($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE temporary_expires = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setTemporaryPass($value = null)
	{
		return self::set($this, 'temporary_pass', $value);
	}

	function getTemporaryPass($vars = null)
	{
		if (!property_exists($this, 'temporary_pass'))
		{
			return null;
		}
		return $this->sanitize('temporary_pass', $this->temporary_pass);
	}

	static function getByTemporaryPass($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE temporary_pass = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setUnit($value = null)
	{
		return self::set($this, 'unit', $value);
	}

	function getUnit($vars = null)
	{
		if (!property_exists($this, 'unit'))
		{
			return null;
		}
		return $this->sanitize('unit', $this->unit);
	}

	static function getByUnit($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE unit = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setZip($value = null)
	{
		return self::set($this, 'zip', $value);
	}

	function getZip($vars = null)
	{
		if (!property_exists($this, 'zip'))
		{
			return null;
		}
		return $this->sanitize('zip', $this->zip);
	}

	static function getByZip($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE zip = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setZip4($value = null)
	{
		return self::set($this, 'zip_4', $value);
	}

	function getZip4($vars = null)
	{
		if (!property_exists($this, 'zip_4'))
		{
			return null;
		}
		return $this->sanitize('zip_4', $this->zip_4);
	}

	static function getByZip4($value, $first_only = false, $include_deleted = false)
	{
		if (!$value || strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE zip_4 = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}
}