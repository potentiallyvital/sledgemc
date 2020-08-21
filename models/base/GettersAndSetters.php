<?php

class GettersAndSetters extends BaseObject
{
	function setAge($value = null)
	{
		self::set($this, 'age', $value);
		return $this;
	}

	function getAge($vars = null)
	{
		if (!property_exists($this, 'age'))
		{
			return null;
		}
		$value = $this->sanitize('age', $this->age);
		if ($vars)
		{
			$value = number_format($this->age);
		}
		return $value;
	}

	static function getByAge($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE age = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setBacktrace($value = null)
	{
		self::set($this, 'backtrace', $value);
		return $this;
	}

	function getBacktrace($vars = null)
	{
		if (!property_exists($this, 'backtrace'))
		{
			return null;
		}
		return $this->sanitize('backtrace', $this->backtrace);
	}

	static function getByBacktrace($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE backtrace = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setBirthday($value = null)
	{
		if (is_numeric($value))
		{
			$value = $this->smartTime($value, 'Y-m-d');
		}
		self::set($this, 'birthday', $value);
		return $this;
	}

	function getBirthday($vars = null)
	{
		if (!property_exists($this, 'birthday'))
		{
			return null;
		}
		if ($vars == '')
		{
			$vars = 'Y-m-d';
		}
		return $this->smartTime($this->birthday, $vars);
	}

	static function getByBirthday($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE birthday = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setBrowser($value = null)
	{
		self::set($this, 'browser', $value);
		return $this;
	}

	function getBrowser($vars = null)
	{
		if (!property_exists($this, 'browser'))
		{
			return null;
		}
		return $this->sanitize('browser', $this->browser);
	}

	static function getByBrowser($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(browser) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setCreated($value = null)
	{
		self::set($this, 'created', $value);
		return $this;
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
		return $this->smartTime($this->created, $vars);
	}

	static function getByCreated($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE created = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setDeleted($value = null)
	{
		self::set($this, 'deleted', $value);
		return $this;
	}

	function getDeleted($vars = null)
	{
		if (!property_exists($this, 'deleted'))
		{
			return null;
		}
		if ($vars == '')
		{
			$vars = 'Y-m-d H:i:s';
		}
		return $this->smartTime($this->deleted, $vars);
	}

	static function getByDeleted($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE deleted = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setDescription($value = null)
	{
		self::set($this, 'description', $value);
		return $this;
	}

	function getDescription($vars = null)
	{
		if (!property_exists($this, 'description'))
		{
			return null;
		}
		return $this->sanitize('description', $this->description);
	}

	static function getByDescription($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE description = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setEmail($value = null)
	{
		self::set($this, 'email', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(email) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setEquipped($value = null)
	{
		self::set($this, 'equipped', $value);
		return $this;
	}

	function getEquipped($vars = null)
	{
		if (!property_exists($this, 'equipped'))
		{
			return null;
		}
		return $this->sanitize('equipped', $this->equipped);
	}

	static function getByEquipped($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE equipped = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setEquipTo($value = null)
	{
		self::set($this, 'equip_to', $value);
		return $this;
	}

	function getEquipTo($vars = null)
	{
		if (!property_exists($this, 'equip_to'))
		{
			return null;
		}
		return $this->sanitize('equip_to', $this->equip_to);
	}

	static function getByEquipTo($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(equip_to) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setFont($value = null)
	{
		self::set($this, 'font', $value);
		return $this;
	}

	function getFont($vars = null)
	{
		if (!property_exists($this, 'font'))
		{
			return null;
		}
		return $this->sanitize('font', $this->font);
	}

	static function getByFont($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(font) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setFromId($value = null)
	{
		self::set($this, 'from_id', $value);
		return $this;
	}

	function getFromId($vars = null)
	{
		if (!property_exists($this, 'from_id'))
		{
			return null;
		}
		$value = $this->sanitize('from_id', $this->from_id);
		if ($vars)
		{
			$value = number_format($this->from_id);
		}
		return $value;
	}

	static function getByFromId($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE from_id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setFromName($value = null)
	{
		self::set($this, 'from_name', $value);
		return $this;
	}

	function getFromName($vars = null)
	{
		if (!property_exists($this, 'from_name'))
		{
			return null;
		}
		return $this->sanitize('from_name', $this->from_name);
	}

	static function getByFromName($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(from_name) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setGender($value = null)
	{
		self::set($this, 'gender', $value);
		return $this;
	}

	function getGender($vars = null)
	{
		if (!property_exists($this, 'gender'))
		{
			return null;
		}
		return $this->sanitize('gender', $this->gender);
	}

	static function getByGender($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE gender = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setHardcore($value = null)
	{
		self::set($this, 'hardcore', $value);
		return $this;
	}

	function getHardcore($vars = null)
	{
		if (!property_exists($this, 'hardcore'))
		{
			return null;
		}
		return $this->sanitize('hardcore', $this->hardcore);
	}

	static function getByHardcore($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE hardcore = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setId($value = null)
	{
		self::set($this, 'id', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setIp($value = null)
	{
		self::set($this, 'ip', $value);
		return $this;
	}

	function getIp($vars = null)
	{
		if (!property_exists($this, 'ip'))
		{
			return null;
		}
		return $this->sanitize('ip', $this->ip);
	}

	static function getByIp($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(ip) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setMethod($value = null)
	{
		self::set($this, 'method', $value);
		return $this;
	}

	function getMethod($vars = null)
	{
		if (!property_exists($this, 'method'))
		{
			return null;
		}
		return $this->sanitize('method', $this->method);
	}

	static function getByMethod($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(method) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setModified($value = null)
	{
		self::set($this, 'modified', $value);
		return $this;
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
		return $this->smartTime($this->modified, $vars);
	}

	static function getByModified($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
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
		self::set($this, 'name', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(name) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setOrientation($value = null)
	{
		self::set($this, 'orientation', $value);
		return $this;
	}

	function getOrientation($vars = null)
	{
		if (!property_exists($this, 'orientation'))
		{
			return null;
		}
		return $this->sanitize('orientation', $this->orientation);
	}

	static function getByOrientation($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE orientation = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParams($value = null)
	{
		self::set($this, 'params', $value);
		return $this;
	}

	function getParams($vars = null)
	{
		if (!property_exists($this, 'params'))
		{
			return null;
		}
		return $this->sanitize('params', $this->params);
	}

	static function getByParams($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE params = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParentClass($value = null)
	{
		self::set($this, 'parent_class', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(parent_class) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setParentId($value = null)
	{
		self::set($this, 'parent_id', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE parent_id = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setPassword($value = null)
	{
		self::set($this, 'password', $value);
		return $this;
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
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE password = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setPlaying($value = null)
	{
		self::set($this, 'playing', $value);
		return $this;
	}

	function getPlaying($vars = null)
	{
		if (!property_exists($this, 'playing'))
		{
			return null;
		}
		return $this->sanitize('playing', $this->playing);
	}

	static function getByPlaying($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE playing = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setQuantity($value = null)
	{
		self::set($this, 'quantity', $value);
		return $this;
	}

	function getQuantity($vars = null)
	{
		if (!property_exists($this, 'quantity'))
		{
			return null;
		}
		$value = $this->sanitize('quantity', $this->quantity);
		if ($vars)
		{
			$value = number_format($this->quantity);
		}
		return $value;
	}

	static function getByQuantity($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE quantity = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setRead($value = null)
	{
		self::set($this, 'read', $value);
		return $this;
	}

	function getRead($vars = null)
	{
		if (!property_exists($this, 'read'))
		{
			return null;
		}
		return $this->sanitize('read', $this->read);
	}

	static function getByRead($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE read = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setReferrer($value = null)
	{
		self::set($this, 'referrer', $value);
		return $this;
	}

	function getReferrer($vars = null)
	{
		if (!property_exists($this, 'referrer'))
		{
			return null;
		}
		return $this->sanitize('referrer', $this->referrer);
	}

	static function getByReferrer($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(referrer) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setSeasonal($value = null)
	{
		self::set($this, 'seasonal', $value);
		return $this;
	}

	function getSeasonal($vars = null)
	{
		if (!property_exists($this, 'seasonal'))
		{
			return null;
		}
		return $this->sanitize('seasonal', $this->seasonal);
	}

	static function getBySeasonal($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE seasonal = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setSessionKey($value = null)
	{
		self::set($this, 'session_key', $value);
		return $this;
	}

	function getSessionKey($vars = null)
	{
		if (!property_exists($this, 'session_key'))
		{
			return null;
		}
		return $this->sanitize('session_key', $this->session_key);
	}

	static function getBySessionKey($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(session_key) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setSize($value = null)
	{
		self::set($this, 'size', $value);
		return $this;
	}

	function getSize($vars = null)
	{
		if (!property_exists($this, 'size'))
		{
			return null;
		}
		$value = $this->sanitize('size', $this->size);
		if ($vars)
		{
			$value = number_format($this->size);
		}
		return $value;
	}

	static function getBySize($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE size = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setSlug($value = null)
	{
		self::set($this, 'slug', $value);
		return $this;
	}

	function getSlug($vars = null)
	{
		if (!property_exists($this, 'slug'))
		{
			return null;
		}
		return $this->sanitize('slug', $this->slug);
	}

	static function getBySlug($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(slug) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setTheme($value = null)
	{
		self::set($this, 'theme', $value);
		return $this;
	}

	function getTheme($vars = null)
	{
		if (!property_exists($this, 'theme'))
		{
			return null;
		}
		return $this->sanitize('theme', $this->theme);
	}

	static function getByTheme($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(theme) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setTimes($value = null)
	{
		self::set($this, 'times', $value);
		return $this;
	}

	function getTimes($vars = null)
	{
		if (!property_exists($this, 'times'))
		{
			return null;
		}
		$value = $this->sanitize('times', $this->times);
		if ($vars)
		{
			$value = number_format($this->times);
		}
		return $value;
	}

	static function getByTimes($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE times = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setTimezone($value = null)
	{
		self::set($this, 'timezone', $value);
		return $this;
	}

	function getTimezone($vars = null)
	{
		if (!property_exists($this, 'timezone'))
		{
			return null;
		}
		return $this->sanitize('timezone', $this->timezone);
	}

	static function getByTimezone($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(timezone) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setType($value = null)
	{
		self::set($this, 'type', $value);
		return $this;
	}

	function getType($vars = null)
	{
		if (!property_exists($this, 'type'))
		{
			return null;
		}
		return $this->sanitize('type', $this->type);
	}

	static function getByType($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE LOWER(type) = '".strtolower(str_replace("'", "''", $value))."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setValueInt($value = null)
	{
		self::set($this, 'value_int', $value);
		return $this;
	}

	function getValueInt($vars = null)
	{
		if (!property_exists($this, 'value_int'))
		{
			return null;
		}
		$value = $this->sanitize('value_int', $this->value_int);
		if ($vars)
		{
			$value = number_format($this->value_int);
		}
		return $value;
	}

	static function getByValueInt($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE value_int = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setValueText($value = null)
	{
		self::set($this, 'value_text', $value);
		return $this;
	}

	function getValueText($vars = null)
	{
		if (!property_exists($this, 'value_text'))
		{
			return null;
		}
		return $this->sanitize('value_text', $this->value_text);
	}

	static function getByValueText($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE value_text = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setX($value = null)
	{
		self::set($this, 'x', $value);
		return $this;
	}

	function getX($vars = null)
	{
		if (!property_exists($this, 'x'))
		{
			return null;
		}
		$value = $this->sanitize('x', $this->x);
		if ($vars)
		{
			$value = number_format($this->x);
		}
		return $value;
	}

	static function getByX($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE x = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setY($value = null)
	{
		self::set($this, 'y', $value);
		return $this;
	}

	function getY($vars = null)
	{
		if (!property_exists($this, 'y'))
		{
			return null;
		}
		$value = $this->sanitize('y', $this->y);
		if ($vars)
		{
			$value = number_format($this->y);
		}
		return $value;
	}

	static function getByY($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE y = '".str_replace("'", "''", $value)."'";
		return self::selectAll($sql, $first_only, $include_deleted);
	}

	function setZ($value = null)
	{
		self::set($this, 'z', $value);
		return $this;
	}

	function getZ($vars = null)
	{
		if (!property_exists($this, 'z'))
		{
			return null;
		}
		$value = $this->sanitize('z', $this->z);
		if ($vars)
		{
			$value = number_format($this->z);
		}
		return $value;
	}

	static function getByZ($value, $first_only = false, $include_deleted = false)
	{
		if (strlen($value) == 0)
		{
			return ($first_only ? null : []);
		}
		$class = get_called_class();
		$object = new $class();
		$sql = "SELECT * FROM $object->table WHERE z = ".round(preg_replace('/[^-.0-9]/', '', $value))."";
		return self::selectAll($sql, $first_only, $include_deleted);
	}
}