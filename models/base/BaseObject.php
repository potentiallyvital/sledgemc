<?php

/**
 * base class for database objects
 * all database classes will extend this class
 * do basic functions like save, delete, set, etc
 */
#[\AllowDynamicProperties]
class BaseObject extends Controller
{
	var $parent;
	var $children = [];

	protected $table = '';
	protected $modified_columns = [];

	/**
	 * constructor function
	 * @param   $params mixed   - int or array of values
	 *			  - if int, return self::getById($id)
	 *			  - if array, save new object with values
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
	static function __set_state($properties = [])
	{
		$class = get_called_class();
		$args = func_get_args();

		$object = new $class();
		$data = array_shift($args);
		foreach ($data as $key => $value)
		{
			$object->$key = $value;
		}
		return $object;
	}

	/**
	 * get child class by id
	 */
	static function getById($id)
	{
		$sql = "SELECT tableoid::regclass AS table
			FROM id
			WHERE id = {$id}";
		$result = selectOne($sql);
		if ($result && !empty($result['table']))
		{
			$class = camelCase($result['table'], true);
			return $class::getById($id);
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
			case 'numeric':
				$sanitized = $value;
				$sanitized = preg_replace('/[^-.0-9]/', '', $sanitized);
				$sanitized = (float)$sanitized;
				$sanitized = round($sanitized, $type[2]);
				break;
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'integer':
			case 'bigint':
				$sanitized = $value;
				$sanitized = preg_replace('/[^-E0-9]/', '', $sanitized);
				$sanitized = (int)$sanitized;
				$sanitized = floor($sanitized);
				$sanitized = round($sanitized);
				if (strlen($sanitized) == 0)
				{
					$sanitized = 0;
				}
				break;
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'timestamp without time zone':
				$sanitized = $value;
				$sanitized = preg_replace('/[a-zA-Z]/', ' ', $sanitized);
				$sanitized = preg_replace('/[^-0-9 :]/', '', $sanitized);
				break;
			case 'character':
			case 'character varying':
				#$sanitized = preg_replace('/[^-_,.a-zA-Z0-9 ~!@#$%^&*()+=|]:;/', '', $value);
				$sanitized = english(trim($value));
				break;
			case 'enum':
				$sanitized = (in_array($value, $type[1]) ? $value : null);
				break;
			case 'blob':
			case 'text':
				$sanitized = english($value);
				break;
			default:
				$sanitized = english($value);
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
	 * overwritten in child classes to inherit parent values
	 */
	function toArray()
	{
		return [];
	}

	/**
	 * get all column types
	 * overwritten in child classes to inherit parent values
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

		return $columns[$name];
	}

	/**
	 * get the base class name with unique columns
	 * overwritten in child classes to inherit parent values
	 */
	static function getUniqueClass()
	{
		return 'BaseObject';
	}

	/**
	 * whether to allow HTML being saved in this field
	 */
	function allowHtml($field = string)
	{
		return false;
	}

	/**
	 * called just before saving an objects data
	 */
	function beforeSave()
	{
		$array = $this->toArray();

		if (array_key_exists('slug', $array) && !empty($this->name))
		{
			$this->setSlug(slugify($this->name));
		}

		$this->setModified(date('Y-m-d H:i:s'));
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

		if (empty($this->created))
		{
			$this->setCreated(date('Y-m-d H:i:s'));
			$this->setModified(date('Y-m-d H:i:s'));
		}

		return true;
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
	function afterDelete($hard = false)
	{
		if (!$hard && !empty($this->log_class))
		{
			$this->moveToLog();
		}
	}

	/**
	 * check if im deleted or not
	 */
	function isDeleted()
	{
		return (!empty($this->deleted) && strtotime($this->deleted) <= time());
	}

	/**
	 * un-delete a record
	 */
	function restore()
	{
		$this->setDeleted(null)->save();
	}

	/**
	 * save updated values to the database if
	 * necessary. do not save if values have not
	 * changed, unless $force is true
	 */
	function save($force = false)
	{
		$id = $this->getUseId();

		if (empty($id) || $this->isNew())
		{
			if ($this->beforeCreate())
			{
				$this->beforeSave();

				$this->doInsert($id, $force);

				$this->afterCreate();
				$this->afterSave();
			}
		}
		else
		{
			$result = $this->beforeSave();
			if (!$force && $result === false)
			{
				return $this;
			}
			if ($force || $this->isModified())
			{
				$this->doUpdate($force);
				$this->afterSave();
			}
		}

		if (!empty($this->parent) && !empty($this->parent->children))
		{
			$this->parent->children[static::CLASS_NAME][$this->id] = $this;
		}

		return $this;
	}

	/**
	 * check if this is a new record
	 */
	function isNew()
	{
		if (empty($this->id))
		{
			return true;
		}
		elseif ($this->id && array_key_exists('id', $this->modified_columns) && empty($this->modified_columns['id']))
		{
			return true;
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
		return array_key_exists($column, $this->modified_columns);
	}

	/**
	 * get the id to use for saving
	 * if someone wants to change an id
	 * let me do it
	 */
	function getUseId()
	{
		if (isset($this->original_id))
		{
			if ($this->original_id != $this->id)
			{
				$sql = "SELECT * FROM {$this->table} WHERE id = {$this->id}";
				$results = self::selectAll($sql);
				if (!empty($results))
				{
					$this->setId($this->original_id);

					throw new Exception('that id is already taken');
				}

				return $this->original_id;
			}
		}

		return $this->id;
	}

	/**
	 * insert a new record into the database
	 */
	function doInsert($id = null, $force = false)
	{
		$id = ($id ?: $this->id);

		$columns = $this->getColumnSql($force, true);

		$sql = "INSERT INTO {$this->table} {$columns} RETURNING id";

		$link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
		$result = pg_fetch_array(pg_query($link, $sql));
		$id = $result['id'];

		$this->id = $id;
	}

	/**
	 * get sql for update statement
	 */
	function getColumnSql($force = false, $for_insert = false)
	{
		$new_keys = [];
		$new_values = [];
		$mod_columns = [];
		$values = $this->toArray();
		foreach ($values as $key => $value)
		{
			if (!$force && !array_key_exists($key, $this->modified_columns))
			{
				continue;
			}

			$column = $this->getColumnType($key);
			$column_type = array_shift($column);
			$column_detail_1 = array_shift($column);
			$column_detail_2 = array_shift($column);
			switch ($column_type)
			{
				case 'jsonb':
					$clean = "'".json_encode($value)."'";
					break;
				case 'integer':
				case 'bigint':
				case 'smallint':
					$clean = preg_replace('/[^-0-9]/', '', ($value ?: ''));
					if (strlen($clean) == 0)
					{
						$clean = 0;
					}
					break;
				case 'numeric':
					$clean = preg_replace('/[^-.0-9]/', '', ($value ?: ''));
					$clean = (float)$clean;
					$clean = number_format($clean, $column_detail_2, '.', '');
					break;
				case 'timestamp':
				case 'timestamp without time zone':
					if ($value)
					{
						$clean = "'".date('Y-m-d H:i:s', strtotime($value))."'";
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
					$value = ($value === null ? '' : $value);
					if (empty($this->html_fields) || !in_array($key, $this->html_fields))
					{
						if (!$force)
						{
							$value = htmlspecialchars($value);
							$value = str_replace('&amp;', '&', $value);
						}
					}
					if ($column_detail_1 && strlen($value) > $column_detail_1)
					{
						$value = substr($value, 0, $column_detail_1);
					}
					$clean = "'".str_replace("'", "''", $value)."'";
					break;
			}

			if ($for_insert)
			{
				$new_keys[] = $key;
				$new_values[] = $clean;
			}
			else
			{
				$mod_columns[] = "$key=$clean";
			}
		}

		if ($for_insert)
		{
			return '('.implode(',', $new_keys).') VALUES ('.implode(',', $new_values).')';
		}
		else
		{
			return implode(',',$mod_columns);
		}
	}

	/**
	 * save values to the database
	 */
	function doUpdate($force = false)
	{
		if ($force)
		{
			$this->setModified(date('Y-m-d H:i:s'));
		}

		$columns = $this->getColumnSql($force, false);

		if ($columns)
		{
			$sql = "UPDATE {$this->table} SET {$columns} WHERE id = {$this->id}";
			$this->execute($sql);
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
				foreach ($result as $key => $value)
				{
					$object->$key = $value;
				}

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
	static function selectOne($sql = '', $first_only = true, $include_deleted = false)
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
			$sql .= " ORDER BY $first_only";
		}

		$link = self::stream($sql);
		while ($object = self::stream($sql, $link))
		{
			if (!$include_deleted && $object->deleted && strtotime($object->deleted) <= time())
			{
				continue;
			}
			return $object;
		}
	}

	/**
	 * return all objects of the database matching $sql
	 * if $sql is not specified, all objects in the table will
	 * be returned
	 */
	static function selectAll($sql = '', $first_only = false, $include_deleted = false)
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
			$sql .= " ORDER BY $first_only";
		}

		$objects = [];
		$link = self::stream($sql);
		while ($object = self::stream($sql, $link))
		{
			if (!$include_deleted && $object->deleted && strtotime($object->deleted) <= time())
			{
				continue;
			}
			if ($first_only === true)
			{
				return $object;
			}
			$objects[] = $object;
		}

		return $objects;
	}

	/**
	 * select all objects from this table and child tables
	 * assign to proper classes while minimizing queries
	 */
	static function selectAllClassified($sql = '', $first_only = false, $include_deleted = false)
	{
		$all = [];

		if (!$sql)
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME;
		}

		$sql = str_replace('*', '*, tableoid::regclass AS table', $sql);
		$results = selectAll($sql);

		$by_table = [];
		foreach ($results as $row)
		{
			$class = camelCase($row['table'], true);
			$uniqueClass = $class::getUniqueClass();
			$table = $uniqueClass::TABLE_NAME;

			$by_table[$table][$row['id']] = $class;
		}

		foreach ($by_table as $table => $ids)
		{
			$baseClass = camelCase($table, true);
			$baseClass = new $baseClass();

			$sql = "SELECT * FROM {$table} WHERE id IN (".implode(',', array_keys($ids)).")";
			$results = $baseClass::selectAll($sql, $first_only, $include_deleted);

			foreach ($results as $object)
			{
				$class = $ids[$object->id];

				$realObject = new $class();
				$realObject->load($object);

				$all[] = $realObject;
			}
		}

		return $all;
	}

	/**
	 * truncate a table
	 * if $quick a simple TRUNCATE {table] will be called
	 * if not, each row will use the classes to delete data
	 * children will also be deleted
	 */
	static function truncate($quick = false)
	{
		if (!$quick)
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME;
			$link = self::stream($sql);
			while ($object = self::stream($sql, $link))
			{
				$object->delete();
			}
		}

		$sql = "TRUNCATE ".static::TABLE_NAME;
		$this->execute($sql);
	}

	/**
	 * load up values for a record from the database
	 */
	function load($data = [])
	{
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$this->$key = $value;
			}
		}
		elseif ($this->id)
		{
			$sql = "SELECT * FROM ".static::TABLE_NAME." WHERE id = {$this->id}";
			$result = selectOne($sql);
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
				$this->setFunc($key, post($key));
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
			if ($column == 'id' && !property_exists($object, 'original_id'))
			{
				$object->original_id = $object->id;
			}

			$object->modified_columns[$column] = $object->$column;
			$object->$column = $value;
		}

		return $object;
	}
	/**
	 * alternate set function
	 */
	function setFunc($column, $value)
	{
		return self::set($this, $column, $value);
	}

	/**
	 * get a clone of this object
	 */
	function getClone($include_id = false, $class = null)
	{
		$class = ($class ?: static::CLASS_NAME);

		$object = new $class();
		$array = $this->toArray();
		if (!$include_id)
		{
			unset($array['id']);
		}
		foreach ($array as $key => $value)
		{
			$object->setFunc($key, $value);
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
			$this->setFun($key, $value);
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
			$object->setFunc($key, $value);
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
	 * get a sluggified version of my $name
	 */
	function getSlug($field = 'name')
	{
		return slugify($this->$field);
	}


	/***************************************
	 *
	 * PARENT/CHILD
	 *
	 */

	/**
	 * get my parent
	 */
	function getParent($ofClass = null, $last = false)
	{
		if (!$ofClass)
		{
			if (empty($this->parent))
			{
				$this->parent = null;
				if ($this->parent_id)
				{
					$class = $this->parent_class;
					$this->parent = new $class($this->parent_id);
				}
			}
			return $this->parent;
		}

		if ($this->parent_id)
		{
			$class = $this->parent_class;
			$parent = new $class($this->parent_id);
			if ($parent instanceof $ofClass && !$last)
			{
				return $parent;
			}

			return $parent->getParent($ofClass, $last);
		}

		if ($this instanceof $ofClass)
		{
			return $this;
		}
	}

	/**
	 * set my parent to an object
	 */
	function setParent($parent)
	{
		if ($parent)
		{
			$this->setParentId($parent->id);
			$this->setParentClass($parent::CLASS_NAME);

			$this->parent = $parent;
		}

		return $this;
	}

	/**
	 * remove me from my parent
	 */
	function orphan()
	{
		$parent = $this->getParent();
		if ($parent)
		{
			unset($parent->children[static::CLASS_NAME][$this->id]);
			unset($parent->children[$this->id]);
		}

		$this->setParentId(0);
		$this->setParentClass('');
		$this->save();

		$this->parent = null;

		return $this;
	}

	/**
	 * clear kids on delete
	 */
	function delete($hard = false)
	{
		$parent = $this->getParent();
		if ($parent)
		{
			unset($parent->children[static::CLASS_NAME][$this->id]);
			unset($parent->children[$this->id]);
		}

		$this->beforeDelete();

		if (!$hard && array_key_exists('deleted', $this->toArray()))
		{
			$this->setDeleted(date('Y-m-d H:i:s'))->save();
		}
		else
		{
			$this->hardDelete();
		}

		$this->afterDelete($hard);

		return $this->deleteChildren($hard);
	}
	function hardDelete()
	{
		$sql = "DELETE FROM ONLY {$this->table} WHERE id = {$this->id}";
		$this->execute($sql);
	}
	function deleteChildren($hard = false)
	{
		$deleted = true;

		foreach (Id::getByParentId($this->id) as $child)
		{
			if (!$child->delete($hard))
			{
				$deleted = false;
			}
		}

		return $deleted;
	}

	/**
	 * get all children of class
	 */
	function getChildren($class = null, $include_deleted = false)
	{
		if (!$this->id)
		{
			return [];
		}

		$table = ($class ? $class::TABLE_NAME : 'id');

		if ($class)
		{
			if (empty($this->children[$class]))
			{
				$this->children[$class] = [];

				$results = selectAll("SELECT * FROM {$table} WHERE parent_id = {$this->id} ORDER BY id ASC");
				foreach ($results as $row)
				{
					$child = new $class();
					$child->load($row);

					$child->parent = $this;

					if ($include_deleted || empty($child->deleted))
					{
						$this->children[$class][$child->id] = $child;
						$this->children[$child->id] = $child;
					}
				}
			}

			return $this->children[$class];
		}
		else
		{
			$results = selectAll("SELECT tableoid::regclass AS table FROM {$table} WHERE parent_id = {$this->id} GROUP BY tableoid::regclass");
			foreach ($results as $row)
			{
				$class = str_replace(' ','',ucwords(str_replace('_',' ',$row['table'])));

				$this->getChildren($class, $include_deleted);
			}

			return $this->children;
		}
	}

	/**
	 * get a single child of class
	 */
	function getChild($class, $create = false)
	{
		if (!$this->id)
		{
			return;
		}

		if (is_numeric($class))
		{
			$id = $class;

			if (!empty($this->children[$id]))
			{
				return $this->children[$id];
			}

			$result = selectOne("SELECT parent_id, tableoid::regclass AS table FROM id WHERE id = {$id}");

			if ($result && $result['parent_id'] == $this->id)
			{
				$class = camelCase($result['table'], true);
				$child = $class::getById($id);

				$child->parent = $this;

				$this->children[$child->id] = $child;

				return $child;
			}
		}
		elseif ($class)
		{
			$children = $this->getChildren($class);
			if ($children)
			{
				return array_shift($children);
			}

			if ($create)
			{
				$child = new $class();
				$child->setParent($this);
				$child->save();

				$child->parent = $this;

				$this->children[$child::CLASS_NAME][$child->id] = $child;
				$this->children[$child->id] = $child;

				return $child;
			}
		}
	}

	/**
	 * increment/decrement a value
	 */
	function increment($field, $by = 1)
	{
		$this->setFunc($field, $this->$field + $by);
		$this->save();
	}
	function decrement($field, $by = 1)
	{
		$this->setFunc($field, $this->$field - $by);
		$this->save();
	}

	/**
	 * get linked stuff
	 */
	function getLinked($linkClass, $itemClass, $first_only = false, $include_deleted = false)
	{
		if ($this->id)
		{
			$link_table = $linkClass::TABLE_NAME;
			$item_table = $itemClass::TABLE_NAME;

			$sql = "SELECT {$item_table}.*
				FROM {$link_table}
				JOIN {$item_table} ON {$item_table}.id = {$link_table}.child_id
				WHERE {$link_table}.parent_id = {$this->id} AND {$link_table}.child_class = '{$itemClass}' ".($include_deleted ? "" : "AND {$link_table}.deleted IS NULL")."
				ORDER BY {$link_table}.id ASC";

			return $itemClass::selectAll($sql, $first_only, $include_deleted);
		}
	}
	function removeLinked($linkClass, $itemClass)
	{
		if ($this->id)
		{
			$link_table = $linkClass::TABLE_NAME;
			$item_table = $itemClass::TABLE_NAME;

			$sql = "SELECT {$link_table}.*
				FROM {$link_table}
				JOIN {$item_table} ON {$item_table}.id = {$link_table}.parent_id
				WHERE {$link_table}.child_id = {$this->id} AND {$link_table}.parent_class = '{$itemClass}'";

			$links = $linkClass::selectAll($sql);
			foreach ($links as $link)
			{
				$link->delete(true);
			}
		}
	}

	/**
	 * move me to a log table
 	 */
	function moveToLog()
	{
		if (!empty($this->log_class))
		{
			$logClass = $this->log_class;

			$log = $logClass::getById($this->id, true, true);
			if (!$log)
			{
				$log = new $logClass();
			}

			if (!empty($this->html_fields))
			{
				$log->html_fields = $this->html_fields;
			}

			foreach ($this->toArray() as $key => $value)
			{
				$log->setFunc($key, $this->$key);
			}
			$log->save();

			$confirmed = $logClass::getById($this->id, true, true);
			if ($confirmed)
			{
				$this->delete(true);
			}
		}
	}

	/**
	 * eventing system
	 */
	function clearEvent($method)
	{
		$event = Event::selectOne("SELECT * FROM event WHERE parent_id = {$this->id} AND method = '{$method}'");
		if ($event)
		{
			$event->delete(true);

			return $event;
		}
	}
	function createEvent($method, $time, $priority = 0, $vars = [])
	{
		return Event::createFor($this, $method, $time, $priority, $vars);
	}

	/**
	 * check if this thing belongs to me
	 */
	function belongsTo($thing)
	{
		return ($this->parent_id == $thing->id);
	}
}
