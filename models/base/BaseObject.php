<?php

/**
 * base class for database objects
 * all database classes will extend this class
 * do basic functions like save, delete, set, etc
 */
class BaseObject extends Controller
{
	// specify which columns are allowed to save as html var $html_fields = [];

	// name of my table
	protected $table = '';

	// keep track of what columns change to determine if save if necessary
	protected $modified_columns = [];

	// keep track of my relationships
	protected $parents = null;
	protected $parents_loaded = [];
	protected $children = null;
	protected $children_loaded = [];

	/**
	 * constructor function
	 * @param   $params mixed   - int or array of values
	 *			    - if int, return self::getById($id)
	 *			    - if array, save new object with values
	 */
	function __construct($params = null)
	{
		parent::__construct($params);

		if (!empty($params))
		{
			if (is_numeric($params))
			{
				$this->id = $params;
				$this->load();
			}
			elseif (is_array($params))
			{
				foreach ($params as $key => $value)
				{
					self::set($this, $key, $value);
				}
				$this->save();
			}
		}

		if (defined(get_class($this).'::TABLE_NAME'))
		{
			$this->table = static::TABLE_NAME;
		}
	}

	/**
	 * for caching purposes
	 */
	static function __set_state($data)
	{
		$class = get_called_class();
		$object = new $class();

		foreach ($data as $key => $value)
		{
			$object->$key = $value;
		}

		return $object;
	}

	/**
	 * load up values for a record from the database
	 */
	function load($data = [])
	{
		if ($data)
		{
			if (is_numeric($data))
			{
				$data = selectOne("SELECT * FROM ".static::TABLE_NAME." WHERE id = {$data}");
			}
			if (is_object($data))
			{
				$data = (array)$data;
			}
			if (is_array($data))
			{
				foreach ($data as $key => $value)
				{
					$this->$key = $value;
				}

				return $this;
			}
		}

		if ($this->id)
		{
			$result = selectOne("SELECT * FROM ".static::TABLE_NAME." WHERE id = {$this->id}");
			if ($result)
			{
				foreach ($result as $key => $value)
				{
					$this->$key = $value;
				}
			}
		}

		$this->table = static::TABLE_NAME;

		return $this;
	}

	/**
	 * gets extended in child classes
	 * if its called here we need to figure out the table/class
	 */
	static function getById($id, $first_only = true)
	{
		$id = (int)$id;

		$sql = "SELECT tableoid::regclass AS table
			FROM ".SLEDGEMC_BASE_TABLE."
			WHERE id = {$id}";
		$result = selectOne($sql);
		if ($result && !empty($result['table']))
		{
			$class = camelCase($result['table'], true);
			return $class::getById($id, $first_only);
		}
	}

	/**
	 * sanitize a value before saving
	 */
	function sanitize($column, $value)
	{
		if ($value === null)
		{
			return $value;
		}

		$type = $this->getColumnType($column);
		switch ($type[0])
		{
			case 'enum':
				$sanitized = (in_array($value, $type[1]) ? $value : null);
				break;
			case 'json':
				$sanitized = json_encode($value);
				break;
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'integer':
			case 'bigint':
				$sanitized = $value;
				$sanitized = floor($value);
				$sanitized = preg_replace('/[^-E0-9]/', '', $sanitized);
				$sanitized = round($sanitized);
				if (strlen($sanitized) == 0)
				{
					$sanitized = 0;
				}
				break;
			case 'numeric':
				$sanitized = $value;
				$sanitized = preg_replace('/[^-.0-9]/', '', $sanitized);
				$sanitized = ($sanitized * pow(10, $type[2]));
				$sanitized = floor($sanitized);
				$sanitized = ($sanitized / pow(10, $type[2]));
				break;
			case 'date':
			case 'datetime':
			case 'timestamp':
				$sanitized = preg_replace('/[^-0-9 :.]/', '', $value);
				break;
/*
			case 'character':
			case 'character varying':
				$sanitized = preg_replace('/[^-_,.a-zA-Z0-9 ~!@#$%^&*()+=|]:;/', '', $value);
				break;
*/
			case 'enum':
				$sanitized = (in_array($value, $type[1]) ? $value : null);
				break;
			case 'blob':
			case 'text':
				$sanitized = $value;
				break;
			default:
				$sanitized = $value;
				break;
		}

		if (!empty($type[1]) && is_numeric($type[1]) && $type[1] > 0)
		{
			if (substr($sanitized, 0, 1) == '-')
			{
				$type[1]++;
			}
			$sanitized = substr($sanitized, 0, $type[1]);
		}

		return $sanitized;
	}

	/**
	 * get me and all my saved values
	 * automatically overwritten in child classes to inherit parent values
	 */
	function toArray()
	{
		return [];
	}

	/**
	 * get all column types
	 * automatically overwritten in child classes to inherit parent values
	 */
	function getColumnTypes()
	{
		return [];
	}

	/**
	 * get type of column
	 */
	function getColumnType($name)
	{
		$columns = $this->getColumnTypes();

		return (isset($columns[$name]) ? $columns[$name] : null);
	}

	/**
	 * called just before saving an objects data
	 */
	function beforeSave()
	{
		// return false if we need to prevent the save
		// always default to return parent::beforeSave();

		$array = $this->toArray();

		if (array_key_exists('modified', $array))
		{
			$this->setModified(now());
		}
	}

	/**
	 * called just after saving an objects data
	 */
	function afterSave()
	{
	}

	/**
	 * called just before inserting a new record
	 */
	function beforeCreate()
	{
		$array = $this->toArray();

		if (array_key_exists('created', $array) && empty($this->created))
		{
			$this->setCreated(now());
		}
	}

	/**
	 * called just after inserting a new record
	 */
	function afterCreate()
	{
	}

	/**
	 * called just before deleting a record
	 */
	function beforeDelete()
	{
	}

	/**
	 * called just after deleting a record
	 */
	function afterDelete()
	{
	}

	/**
	 * delete a record from the database
	 * set to deleted and do not delete if column exists
	 */
	function delete()
	{
		if ($this->id !== null && is_numeric($this->id))
		{
			$this->beforeDelete();

			$this->deleteChildren();
			$this->execute("DELETE FROM ONLY {$this->table} WHERE id = {$this->id}");

			$this->afterDelete();
		}
	}

	/**
	 * retrieve all of my children
	 */
	function getChild($ofClass = null, $last = false)
	{
		$children = $this->getChildren($ofClass);

		if ($last)
		{
			return array_pop($children);
		}
		else
		{
			return array_shift($children);
		}
	}
	function getChildren($ofClass = null)
	{
		// only load all children once
		if ($this->children === null)
		{
			$this->children = [];
			$this->children_loaded[] = 'All';

			if ($this->id)
			{
				$by_table = [];
				$sql = "SELECT child_id, child_table FROM ".SLEDGEMC_CHILD_TABLE." WHERE parent_id = {$this->id}";
				$results = selectAll($sql);
				foreach ($results as $row)
				{
					$by_table[$row['child_table']][] = $row['child_id'];
				}

				foreach ($by_table as $child_table => $child_ids)
				{
					$model = camelCase($child_table, true);

					foreach ($model::selectAll("SELECT * FROM {$child_table} WHERE id IN (".implode(',', $child_ids).")") as $child)
					{
						$this->children[$child->id] = $child;
					}

					$this->children_loaded[] = $model;
				}

				ksort($this->children);
			}
		}

		if ($ofClass)
		{
			$children = [];
			foreach ($this->children as $child)
			{
				if ($child->inherits($ofClass))
				{
					$children[$child->id] = $child;
				}
			}

			return $children;
		}
		else
		{
			return $this->children;
		}
	}

	/**
	 * return my parent
	 */
	function getParent($ofClass = null, $last = false)
	{
		$parents = $this->getParents($ofClass);

		if ($last)
		{
			return array_pop($parents);
		}
		else
		{
			return array_shift($parents);
		}
	}
	function getParents($ofClass = null)
	{
		// only load all parents once
		if ($this->parents === null)
		{
			$this->parents = [];
			$this->parents_loaded[] = 'All';

			if ($this->id)
			{
				$by_table = [];
				$sql = "SELECT parent_id, parent_table FROM ".SLEDGEMC_CHILD_TABLE." WHERE child_id = {$this->id}";
				$results = selectAll($sql);
				foreach ($results as $row)
				{
					$by_table[$row['parent_table']][] = $row['parent_id'];
				}

				foreach ($by_table as $parent_table => $parent_ids)
				{
					$model = camelCase($parent_table, true);

					foreach ($model::selectAll("SELECT * FROM {$parent_table} WHERE id IN (".implode(',', $parent_ids).")") as $parent)
					{
						$this->parents[$parent->id] = $parent;
					}

					$this->parents_loaded[] = $model;
				}

				ksort($this->parents);
			}
		}

		if ($ofClass)
		{
			$parents = [];
			foreach ($this->parents as $parent)
			{
				if ($parent->inherits($ofClass))
				{
					$parents[$parent->id] = $parent;
				}
			}

			return $parents;
		}
		else
		{
			return $this->parents;
		}
	}

	/**
	 * make this object belong to another
	 */
	function linkTo($object)
	{
		$link = $this->getLinkTo($object);

		if (!$link)
		{
			$model = camelCase(SLEDGEMC_CHILD_TABLE, true);

			$link = new $model();
			$link->setParent($object);
			$link->setChild($this);
			$link->save();
		}

		return $link;
	}

	/**
	 * make this object no longer belong to another
	 */
	function unlinkFrom($object, $delete_orphan = true)
	{
		$link = $this->getLinkTo($object);

		if ($link)
		{
			if ($delete_orphan && $link->isOnlyChild())
			{
				$child = $link->getChild();
				$child->delete();
			}

			$link->delete();
		}
	}

	/**
	 * retrieve an existing relationship
	 */
	function getLinkTo($object)
	{
		if ($this->id && $object->id)
		{
			$model = camelCase(SLEDGEMC_CHILD_TABLE, true);
			return $model::selectOne("SELECT * FROM ".SLEDGEMC_CHILD_TABLE." WHERE parent_id = {$object->id} AND child_id = {$this->id}");
		}
	}

	/**
	 * do i only belong to one record?
	 */
	function isOnlyChild()
	{
		if ($this->id)
		{
			$results = selectAll("SELECT id FROM ".SLEDGEMC_CHILD_TABLE." WHERE child_id = {$this->id} LIMIT 2");
			if (count($results) == 2)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * delete all of a records children
	 * called just after afterDelete
	 */
	function deleteChildren()
	{
		foreach ($this->getChildren() as $child)
		{
			$child->unlinkFrom($this);

			if ($child->isOnlyChild())
			{
				$child->delete();
			}
		}
	}

	/**
	 * save updated values to the database if
	 * necessary. do not save if values have not
	 * changed, unless $force is true
	 */
	function save($force = false)
	{
		if (empty($this->id))
		{
			$this->beforeCreate();
			$this->insertNew();
			$this->beforeSave();
			$this->doSave($force);
			$this->afterCreate();
			$this->afterSave();
		}
		else
		{
			if ($force || $this->isModified())
			{
				$this->beforeSave();
				$this->doSave($force);
				$this->afterSave();
			}
		}

		return $this;
	}

	/**
	 * rekey an object
	 */
	function rekey($new_id)
	{
		if ($this->id && $new_id && is_numeric($this->id) && is_numeric($new_id))
		{
			execute("UPDATE ".SLEDGEMC_CHILD_TABLE." SET parent_id = {$new_id} WHERE parent_id = {$this->id}");
			execute("UPDATE ".SLEDGEMC_CHILD_TABLE." SET child_id = {$new_id} WHERE child_id = {$this->id}");
			execute("UPDATE ".SLEDGEMC_BASE_TABLE." SET id = {$new_id} WHERE id = {$this->id}");

			$this->id = $new_id;
		}
	}

	/**
	 * check if i've been updated
	 */
	function isModified()
	{
		return $this->modified_columns;
	}

	/**
	 * check if a column has been updated
	 */
	function hasModified($column)
	{
		return in_array($column, $this->modified_columns);
	}

	/**
	 * insert a new record into the database
	 */
	function insertNew($id = null)
	{
		// make insert SQL
		if ($id)
		{
			$sql = "INSERT INTO {$this->table} (id) VALUES ({$id}) RETURNING id";
		}
		else
		{
			$sql = "INSERT INTO {$this->table} DEFAULT VALUES RETURNING id";
		}

		// get new id
		$link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
		$result = pg_fetch_array(pg_query($link, $sql));
		$this->id = $result['id'];
	}

	/**
	 * get sql for update statement
	 */
	function getColumnSql($modified_only = false, $bypass_html = false)
	{
		$columns = [];
		$values = $this->toArray();
		foreach ($values as $key => $value)
		{
			if ($modified_only && !in_array($key, $this->modified_columns))
			{
				continue;
			}
			$column = $this->getColumnType($key);
			switch ($column[0])
			{
				case 'json':
					$clean = "'".json_encode($value)."'";
					break;
				case 'integer':
				case 'bigint':
				case 'smallint':
					$clean = preg_replace('/[^-0-9]/', '', $value);
					if (strlen($clean) == 0)
					{
						$clean = 0;
					}
					break;
				case 'numeric':
					$clean = number_format($value, $column[2], '.', '');
					break;
				case 'timestamp':
				case 'timestamp with time zone':
				case 'timestamp without time zone':
					if ($value)
					{
						$clean = "'".now($value)."'";
					}
					else
					{
						$clean = 'NULL';
					}
					break;
				case 'character':
				case 'character varying':
				case 'text':
				default:
					if (empty($this->html_fields) || !in_array($key, $this->html_fields))
					{
						if (!$bypass_html)
						{
							$value = htmlspecialchars($value);
							$value = str_replace('&amp;', '&', $value);
						}
					}
					$clean = "'".str_replace("'", "''", $value)."'";
					break;
			}
			$columns[] = "$key=$clean";
		}

		return implode(',',$columns);
	}

	/**
	 * save values to the database
	 */
	function doSave($force = false)
	{
		$columns = $this->getColumnSql(true, $force);

		if ($columns)
		{
			$this->execute("UPDATE {$this->table} SET {$columns} WHERE id = {$this->id}");
		}

		$this->modified_columns = [];
	}

	/**
	 * execute a database query
	 */
	function execute($sql)
	{
		$link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);

		return pg_query($link, $sql);
	}

	/**
	 * retrieve objects from the database
	 * usage:
	 *   $link = Users::stream($sql);
	 *   while ($user = Users::stream($link)) {
	 *      $user->doSomething();
	 *   }
	 */     
	static function stream($sql, $result = null)
	{
		if (!$result)
		{
			$link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
			$result = pg_query($link, $sql);
			if ($result === false)
			{
				echo '<h3>BAD QUERY:</h3>';
				dump($sql, false);
			}

			return $result;
		}

		if ($result !== true && $result !== false)
		{
			$result = pg_fetch_assoc($result);
			if ($result)
			{
				$class = static::CLASS_NAME;
				$object = new $class();
				$object->load($result);
				return $object;
			}
		}
	}

	/**
	 * get id name array of all objects
	 */
	static function getIdNameArray($column = 'name', $allow_empty = false)
	{
		$objects = self::selectAll("SELECT * FROM ONLY ".static::TABLE_NAME, $column);

		$array = [];
		if ($allow_empty)
		{
			$array[''] = 'None';
		}
		foreach ($objects as $object)
		{
			$array[$object->id] = $object->name;
		}

		return $array;
	}

	/**
	 * return the first object from the database matching $sql
	 */
	static function selectOne($sql = '', $first_only = true)
	{
		if (!$sql)
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME;
		}
		elseif ($sql == 'only')
		{
			$sql = "SELECT * FROM ONLY ".static::TABLE_NAME;
		}

		if ($first_only && $first_only !== true)
		{
			$sql .= " ORDER BY {$first_only}";
		}

		$link = self::stream($sql);
		while ($object = self::stream($sql, $link))
		{
			return $object;
		}
	}

	/**
	 * return all objects of the database matching $sql
	 * if $sql is not specified, all objects in the table will
	 * be returned
	 */
	static function selectAll($sql = '', $first_only = false)
	{
		if (!$sql)
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME;
		}
		elseif ($sql == 'only')
		{
			$sql = "SELECT * FROM ONLY ".static::TABLE_NAME;
		}

		if ($first_only && $first_only !== true)
		{
			$sql .= " ORDER BY {$first_only}";
		}

		$objects = [];
		$link = self::stream($sql);
		while ($object = self::stream($sql, $link))
		{
			if ($first_only === true)
			{
				return $object;
			}
			$objects[] = $object;
		}

		return $objects;
	}

	/**
	 * truncate a table
	 * if $quick a simple TRUNCATE {table] will be called
	 * if not, each row will use the classes to delete data
	 * children will also be deleted
	 */
	static function truncate($quick = false)
	{
		if ($quick)
		{
			$this->execute("TRUNCATE ".static::TABLE_NAME);
		}
		else
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME;
			$link = self::stream($sql);
			while ($object = self::stream($sql, $link))
			{
				$object->delete();
			}
		}
	}

	/**
	 * load up my values from POST
	 */
	function populateFromPost($fields = [])
	{
		$array = $this->toArray();

		if (empty($fields))
		{
			$fields = array_keys($array);
		}

		$post = post();
		foreach ($array as $key => $value)
		{
			if (in_array($key, $fields) && isset($post[$key]))
			{
				self::set($this, $key, post($key));
			}
		}

		return $this;
	}

	/**
	 * base setter function
	 */
	static function set($object, $column, $value)
	{
		if (!property_exists($object, $column))
		{
			return;
		}

		$value = $object->sanitize($column, $value);

		if ($object->$column !== $value)
		{
			$object->modified_columns[$column] = $column;
			$object->$column = $value;
		}

		return $object;
	}

	/**
	 * get a simple version of this object
	 */
	function getCleanObject()
	{
		$class = static::CLASS_NAME;

		$object = new $class();
		$array = $this->toArray();

		foreach ($array as $key => $value)
		{
			$object->$key = $value;
		}

		unset($object->modified_columns);

		return $object;
	}

	/**
	 * get a clone of this object
	 */
	function getClone($include_id = false)
	{
		$class = static::CLASS_NAME;

		$object = new $class();
		$array = $this->toArray();
		if (!$include_id)
		{
			unset($array['id']);
		}
		foreach ($array as $key => $value)
		{
			self::set($object, $key, $value);
		}

		return $object;
	}

	/**
	 * match this object values from another
	 */
	function cloneFrom($object, $include_id = false)
	{
		$array = $object->toArray();
		if (!$include_id)
		{
			unset($array['id']);
		}
		foreach ($array as $key => $value)
		{
			self::set($this, $key, $value);
		}

		return $this;
	}

	/**
	 * match this object values to another
	 */
	function cloneTo($object, $include_id = false)
	{
		$array = $this->toArray();
		if (!$include_id)
		{
			unset($array['id']);
		}
		foreach ($array as $key => $value)
		{
			self::set($object, $key, $value);
		}

		return $this;
	}

	/**
	 * check if i'm an instanceof
	 * allow Class strings
	 */
	function inherits($classes)
	{
		if (!is_array($classes))
		{
			$classes = [$classes];
		}

		foreach ($classes as $class)
		{
			if (is_object($class))
			{
				$class = $class::TABLE_NAME;
			}

			if (defined(get_class($this).'::CLASS_NAME') && static::CLASS_NAME == $class)
			{
				return true;
			}
			elseif (is_subclass_of($this, $class))
			{
				return true;
			}
		}
	}

	/**
	 * normalize a date
	 */
	function formatTime($date, $format = 'Y-m-d H:i:s.u')
	{
		$new_date = str_replace('/', '-', $date);
		$parts = explode('-', $new_date);
		if (count($parts) == 3 && strlen($parts[2]) == 4)
		{
			$time = strtotime($parts[2].'-'.$parts[0].'-'.$parts[1]);
		}
		else
		{
			$time = strtotime($date);
		}
		if ($time && $format)
		{
			$time = date($format, $time);
		}
		return $time;
	}

	/**
	 * increment/decrement a value
	 */
	function increment($field, $by = 1)
	{
		if (empty($this->$field))
		{
			self::set($this, $field, 0);
			$this->save();
		}

		$result = selectOne("UPDATE ".static::TABLE_NAME." SET {$field} = {$field} + {$by} WHERE id = {$this->id} RETURNING {$field}");
		if ($result)
		{
			$this->$field = $result[$field];
		}
	}
	function decrement($field, $by = 1)
	{
		if (empty($this->$field))
		{
			self::set($this, $field, 0);
			$this->save();
		}

		$result = selectOne("UPDATE ".static::TABLE_NAME." SET {$field} = {$field} - {$by} WHERE id = {$this->id} RETURNING {$field}");
		if ($result)
		{
			$this->$field = $result[$field];
		}
	}
}
