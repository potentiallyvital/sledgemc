<?php

/**
 * base class for database objects
 * all database classes will extend this class
 * do basic functions like save, delete, set, etc
 */
class BaseObject extends Controller
{
        protected $table = '';

        protected $modified_columns = [];

        /**
         * constructor function
         * @param   $params mixed   - int or array of values
         *                          - if int, return self::getById($id)
         *                          - if array, save new object with values
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
        static function __set_state()
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
                        case 'tinyint':
                        case 'smallint':
                        case 'mediumint':
                        case 'int':
                        case 'integer':
                        case 'bigint':
                        case 'numeric':
				$sanitized = $value;
				$sanitized = floor($value);
                                $sanitized = preg_replace('/[^-E0-9]/', '', $sanitized);
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
                                $sanitized = preg_replace('/[^-0-9 :]/', '', $value);
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

                if (array_key_exists('slug', $array))
                {
                        $this->setSlug(slugify($this->name));
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

                if (empty($this->created))
                {
                        $this->setCreated(date('Y-m-d H:i:s'));
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
         * delete a record from the database
         * set to deleted and do not delete if column exists
         */
        function delete($hard = false)
        {
                if ($this->id !== null)
                {
			if ($hard == 'quick')
			{
				execute("DELETE FROM ".static::TABLE_NAME." WHERE id = {$this->id}");
				return;
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

                        $this->afterDelete();
                        $this->deleteChildren();
                }
        }

        /**
         * delete all of a records children
         * called just after afterDelete
         */
        function deleteChildren()
        {
        }

        /**
         * remove a record from the database
         */
        function hardDelete()
        {
                $sql = "DELETE FROM ONLY {$this->table} WHERE id = {$this->id}";
                $this->execute($sql);

                $this->hardDeleteChildren();
        }

        /**
         * delete all of a records children
         * called just after hardDelete
         */
        function hardDeleteChildren()
        {
        }

        /**
         * save updated values to the database if
         * necessary. do not save if values have not
         * changed, unless $force is true
         */
        function save($force = false)
        {
                $id = $this->getUseId();

                if (empty($id))
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
                        $result = $this->beforeSave();
                        if (!$force && $result === false)
                        {
                                return $this;
                        }
                        if ($force || $this->isModified())
                        {
                                $this->doSave($force);
                                $this->afterSave();
                        }
                }

                return $this;
        }

        /**
         * save myself to a different table     
         */
        function saveTo($new_table)
        {
                if ($this->id)
                {
                        $this->hardDelete();
                }

                $old_table = $this->table;

                $this->table = $new_table;

                if ($this->id)
                {
                        $this->hardDelete();
                }

                $this->beforeCreate();
                $this->insertNew($this->id);
                $this->beforeSave();

                $this->setModified(date('Y-m-d H:i:s'));

                $columns = $this->getColumnSql();

                if ($columns)
                {
                        $sql = "UPDATE {$this->table} SET {$columns} WHERE id = {$this->id}";
                        $this->execute($sql);
                }

                $this->modified_columns = [];

                $this->doSave();
                $this->afterCreate();
                $this->afterSave();

                $newClass = camelCase($new_table);
                $new = $newClass::getById($this->id);

                $this->table = $old_table;

                return $new;
        }

        /**
         * rekey an object
         */
        function rekey($new_id)
        {
                $sql = "UPDATE id SET parent_id = {$new_id} WHERE parent_id = {$this->id}";
                execute($sql);

                $sql = "UPDATE id SET id = {$new_id} WHERE id = {$this->id}";
                execute($sql);

                $this->id = $new_id;
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
        function insertNew($id = null)
        {
                $date = date('Y-m-d H:i:s');
                if ($id)
                {
                        $sql = "INSERT INTO {$this->table} (id) VALUES ({$id}) RETURNING id";
                }
                else
                {
                        $sql = "INSERT INTO {$this->table} DEFAULT VALUES RETURNING id";
                }
                $link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
                $result = pg_fetch_array(pg_query($link, $sql));
                $id = $result['id'];
                $this->setId($id);
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
                                case 'jsonb':
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
                $this->setModified(date('Y-m-d H:i:s'));

                $columns = $this->getColumnSql(true, $force);

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
        static function selectAllClassified($sql = null)
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
                        $results = $baseClass::selectAll($sql);

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
                        $object->modified_columns[$column] = $column;
                        $object->$column = $value;
                }

                return $object;
        }
	/**
	 * alternate set function
	 */
	function setFunc($column, $value)
	{
		self::set($this, $column, $value);
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
}
