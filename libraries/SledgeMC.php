<?php

/**
 * build ORM classes
 */
class SledgeMC
{
	// line spacing for generated files
	const TAB = "\t";

	// line ending for generated files
	const EOL = "\r\n";

	/**
	 * include x tabs
	 */
	static function tab($times = 1)
	{
		$tab = self::TAB;
		for ($i=1; $i<$times; $i++)
		{
			$tab .= self::TAB;
		}

		return $tab;
	}

	/**
	 * include end of line
	 */
	static function eol($times = 1)
	{
		$eol = self::EOL;
		for ($i=1; $i<$times; $i++)
		{
			$eol .= self::EOL;
		}

		return $eol;
	}

	/**
	 * build the files
	 */
	static function buildFromDatabase()
	{
		$tables = self::getTables(false);
		$children = self::getChildren();

		$path = SLEDGEMC_PATH.'/models';

		foreach ($tables as $table => $columns)
		{
			$className = self::camelCase($table);

			$main_file = $path.'/'.$className.'.php';
			$base_file = $path.'/base/'.$className.'Base.php';

			if (!file_exists($main_file))
			{
				echo "\r\nNEW - {$main_file}";
				self::saveFile($main_file, self::getClass($table));
			}

			if (!file_exists($base_file))
			{
				echo "\r\nNEW - {$base_file}";
				self::saveFile($base_file, self::getBaseClass($table, $columns, $children));
			}
			else
			{
				echo "\r\nOverwrite {$base_file}";
				self::saveFile($base_file, self::getBaseClass($table, $columns, $children));
			}
		}

		$getters_and_setters = $path.'/base/GettersAndSetters.php';
		$columns = self::getColumns();

		echo "\r\nOverwrite {$getters_and_setters}";
		self::saveFile($getters_and_setters, self::getGettersAndSettersClass($columns));

		echo "\r\n\r\nFiles updated.\r\n";
		echo "\r\n=====================================\r\n";

		self::findOldFiles();
	}

	/**
	 * save a file
	 */
	static function saveFile($path, $php)
	{
		file_put_contents($path, $php);
	}

	/**
	 * get all tables and columns from the database
	 */
	static function getTables($include_inherited = true)
	{
		$tables = [];
		$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public'";
		$link = self::query($sql);
		while ($row = self::query($sql, $link))
		{
			$tables[$row['table_name']] = [];
		}
		foreach ($tables as $table => $columns)
		{
			$inherited = [];
			if (!$include_inherited)
			{
				$parent = self::getParentTable($table);
				if ($parent)
				{
					$sql = "SELECT * FROM information_schema.columns WHERE table_name = '$parent' AND table_schema = 'public'";
					$link = self::query($sql);
					while ($row = self::query($sql, $link))
					{
						$inherited[] = strtolower($row['column_name']);
					}
				}
			}
			$sql = "SELECT * FROM information_schema.columns WHERE table_name = '$table' AND table_schema = 'public'";
			$link = self::query($sql);
			while ($row = self::query($sql, $link))
			{
				$name = strtolower($row['column_name']);
				if (in_array($name, $inherited))
				{
					continue;
				}
				$type = $row['data_type'];
				$config = (!empty($type_parts) ? str_replace(')', '', array_pop($type_parts)) : 'null');
				switch ($type)
				{
					case 'smallint':
						$config = '5';
						break;
					case 'integer':
						$config = '11';
						break;
					case 'bigint':
						$config = '20';
						break;
					case 'numeric':
						$config = $row['numeric_precision'].', '.$row['numeric_scale'];
						break;
					case 'text':
					case 'bytea':
					case 'date':
						$config = '';
						break;
					case 'json':
					case 'jsonb':
						$type = 'json';
						$config = '';
						break;
					case 'timestamp':
					case 'timestamp with time zone':
					case 'timestamp without time zone':
						$type = 'timestamp';
						$config = '';
						break;
					case 'character':
					case 'character varying':
						$config = $row['character_maximum_length'];
						break;
					case 'USER-DEFINED':
						$type = 'enum';
						$types = [];
						$type_sql = "SELECT unnest(enum_range(NULL::{$row['udt_name']})) AS value_type";
						$type_link = self::query($type_sql);
						while ($type_row = self::query($type_sql, $type_link))
						{
							$types[] = $type_row['value_type'];
						}
						$config = str_replace('"', "'", json_encode($types));
						break;
					default:
						die("UNKNOWN COLUMN TYPE '$type' IN ".__FILE__." LINE ".__LINE__."\r\n");
				}
				$default = ($row['column_default'] ?: '');
				if (substr($default, 0, 7) == 'nextval')
				{
					$default = '';
				}
				elseif (stristr($default, '()'))
				{
					$default = '';
				}
				$default = explode('::', $default);
				$default = array_shift($default);
				$default = ($default ?: 'null');
				$tables[$table][] = ['name' => $name, 'type' => $type, 'config' => $config, 'default' => $default];
			}
		}

		return $tables;
	}

	/**
	 * get all column names and types from the database
	 */
	static function getColumns()
	{
		$columns = [];
		$sql = "SELECT column_name AS name, data_type AS type
			FROM information_schema.columns
			WHERE table_schema = 'public'
			GROUP BY column_name, data_type
			ORDER BY column_name, data_type";
		$link = self::query($sql);
		while ($row = self::query($sql, $link))
		{
			$type = explode('(', $row['type']);
			$type = array_shift($type);
			$columns[$row['name']] = $type;
		}

		return $columns;
	}

	/**
	 * get foreign key relationships for children
	 */
	static function getChildren()
	{
		$children = [];
		$sql = "SELECT column_name AS name, data_type AS type, table_name
			FROM information_schema.columns
			WHERE table_schema = 'public' AND column_name LIKE '%_id'
			GROUP BY column_name, data_type, table_name
			ORDER BY column_name";
		$link = self::query($sql);
		while ($row = self::query($sql, $link))
		{
			$column = $row['name'];
			$table = $row['table_name'];
			if ($column != rtrim($table, 's').'_id')
			{
				$children[$column][] = $table;
			}
		}

		return $children;
	}

	/**
	 * get the name of the table that a table inherits
	 */
	static function getParentTable($table_name)
	{
		$sql = "SELECT inhparent::regclass AS parent_table
			FROM pg_catalog.pg_inherits
			WHERE inhrelid = '{$table_name}'::regclass";
		$link = self::query($sql);
		while ($row = self::query($sql, $link))
		{
			return $row['parent_table'];
		}
	}

	/**
	 * get results from the database
	 */
	static function query($sql, $result = null)
	{
		$row = true;
		if (!$result)
		{
			$link = pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
			@$result = pg_query($link, $sql);

			return $result;
		}
		if ($result !== true && $result !== false)
		{
			$row = pg_fetch_assoc($result);
		}

		return $row;
	}

	/**
	 * camelcase a string
	 */
	static function camelCase($string)
	{
		$string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
		$string = ucwords(trim($string));
		$string = lcfirst(str_replace(' ', '', $string));
		$string = ucwords($string);

		return $string;
	}

	/**
	 * uncamelcase a string
	 */
	static function unCamelCase($string)
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
		$strings = $matches[0];
		foreach ($strings as &$match)
		{
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}

		return implode('_', $strings);
	}

	/**
	 * construct php for a wORM class
	 */
	static function getClass($table)
	{
		$className = self::camelCase($table);
		$php = '<?php';
		$php .= self::eol(2);
		$php .= 'class '.$className.' extends '.$className.'Base'.self::eol();
		$php .= '{';
		$php .= self::eol(2);
		$php .= '}';

		return $php;
	}

	/**
	 * construct php for a wORM base class
	 */
	static function getBaseClass($table, $columns, $children)
	{
		$parent_table = self::getParentTable($table);
		$parentClass = ($parent_table ? self::camelCase($parent_table) : 'GettersAndSetters');

		$parentClass = 'GettersAndSetters';
		$require = "base/GettersAndSetters.php";
		if ($parent_table)
		{
			$parentClass = self::camelCase($parent_table);
			$require = $parentClass.'.php';
		}

		$className = self::camelCase($table);
		$foreign_key = $table.'_id';
		$php = '';
		$php .= '<?php';
		$php .= self::eol(2);
		#$php .= 'require_once SLEDGEMC_PATH.\'/models/'.$require.'\';'.self::eol();
		#$php .= self::eol();
		$php .= 'class '.$className.'Base extends '.$parentClass.''.self::eol();
		$php .= '{'.self::eol();
		$php .= self::tab().'const CLASS_NAME = \''.$className.'\';'.self::eol();
		$php .= self::tab().'const TABLE_NAME = \''.$table.'\';'.self::eol();
		if ($columns)
		{
			$php .= self::eol();
			foreach ($columns as $column)
			{
				$php .= self::tab().'var $'.$column['name'].' = '.$column['default'].';'.self::eol();
			}
			$php .= self::eol();
			$php .= self::tab().'function toArray()'.self::eol();
			$php .= self::tab().'{'.self::eol();
			$php .= self::tab(2).'$array = [];'.self::eol();
			foreach ($columns as $column)
			{
				$php .= self::tab(2).'$array[\''.$column['name'].'\'] = $this->'.$column['name'].';'.self::eol();
			}
			$php .= self::eol();
			$php .= self::tab(2).'foreach (parent::toArray() as $key => $value)'.self::eol();
			$php .= self::tab(2).'{'.self::eol();
			$php .= self::tab(3).'$array[$key] = $value;'.self::eol();
			$php .= self::tab(2).'}'.self::eol();
			$php .= self::eol();
			$php .= self::tab(2).'return $array;'.self::eol();
			$php .= self::tab().'}'.self::eol();
			$php .= self::eol();
			$php .= self::tab().'function getColumnTypes()'.self::eol();
			$php .= self::tab().'{'.self::eol();
			$php .= self::tab(2).'$columns = parent::getColumnTypes();'.self::eol();
			$php .= self::eol();
			foreach ($columns as $column)
			{
				$php .= self::tab(2).'$columns[\''.$column['name'].'\'] = [\''.$column['type'].'\''.($column['config'] ? ', '.$column['config'] : '').'];'.self::eol();
			}
			$php .= self::eol();
			$php .= self::tab(2).'return $columns;'.self::eol();
			$php .= self::tab().'}'.self::eol();
		}

		$php .= '}';

		return $php;
	}

	/**
	 * build the getters and setters file
	 */
	static function getGettersAndSettersClass($columns)
	{
		$php = '<?php';
		$php .= self::eol(2);
		#$php .= 'require_once SLEDGEMC_PATH.\'/models/base/BaseObject.php\';'.self::eol();
		#$php .= self::eol();
		$php .= 'class GettersAndSetters extends BaseObject'.self::eol();
		$php .= '{';
		foreach ($columns as $column => $type)
		{
			$php .= self::getSetFunction($column, $type);
			$php .= self::getGetFunction($column, $type);
			$php .= self::getGetByFunction($column, $type);
		}
		$php .= '}';

		return $php;
	}

	/**
	 * get php for setting a column
	 */
	static function getSetFunction($column, $type)
	{
		$php = self::eol();
		$php .= self::tab().'function set'.self::camelCase($column).'($value = null)'.self::eol();
		$php .= self::tab().'{'.self::eol();
		switch ($type)
		{
			case 'date':
				$php .= self::tab(2).'if (is_numeric($value))'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$value = $this->formatTime($value, \'Y-m-d\');'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				break;
			case 'datetime':
				$php .= self::tab(2).'if (is_numeric($value))'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$value = $this->formatTime($value, \'Y-m-d H:i:s\');'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				break;
			case 'timestamp':
				$php .= self::tab(2).'if (is_numeric($value))'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$value = $this->formatTime($value, \'Y-m-d H:i:s.u\');'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				break;
		}
		$php .= self::tab(2).'return self::set($this, \''.$column.'\', $value);'.self::eol();
		$php .= self::tab().'}'.self::eol();

		return $php;
	}

	/**
	 * build php for get column
	 */
	static function getGetFunction($column, $type)
	{
		$php = self::eol();
		$php .= self::tab().'function get'.self::camelCase($column).'($vars = null)'.self::eol();
		$php .= self::tab().'{'.self::eol();
		$php .= self::tab(2).'if (!property_exists($this, \''.$column.'\'))'.self::eol();
		$php .= self::tab(2).'{'.self::eol();
		$php .= self::tab(3).'return null;'.self::eol();
		$php .= self::tab(2).'}'.self::eol();
		switch ($type)
		{
			case 'datetime':
			case 'timestamp':
			case 'timestamp with time zone':
			case 'timestamp without time zone':
				$php .= self::tab(2).'if ($vars == \'\')'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$vars = \'Y-m-d H:i:s\';'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				$php .= self::tab(2).'return $this->formatTime($this->'.$column.', $vars);'.self::eol();
				break;
			case 'date':
				$php .= self::tab(2).'if ($vars == \'\')'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$vars = \'Y-m-d\';'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				$php .= self::tab(2).'return $this->formatTime($this->'.$column.', $vars);'.self::eol();
				break;
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'bigint':
				$php .= self::tab(2).'$value = $this->sanitize(\''.$column.'\', $this->'.$column.');'.self::eol();
				$php .= self::tab(2).'if ($vars)'.self::eol();
				$php .= self::tab(2).'{'.self::eol();
				$php .= self::tab(3).'$value = number_format($this->'.$column.');'.self::eol();
				$php .= self::tab(2).'}'.self::eol();
				$php .= self::tab(2).'return $value;'.self::eol();
				break;
			default:
				$php .= self::tab(2).'return $this->sanitize(\''.$column.'\', $this->'.$column.');'.self::eol();
				break;
		}
		$php .= self::tab().'}'.self::eol();

		return $php;
	}

	/**
	 * build php for getByColumn
	 */
	static function getGetByFunction($column, $type)
	{
		// getByColumn()
		$first_only = ($column == 'id' ? 'true' : 'false');

		switch ($type)
		{
			case 'integer':
			case 'bigint':
				$where = $column.' = ".round(preg_replace(\'/[^-.0-9]/\', \'\', $value))."';
				break;
			case 'numeric':
				$where = $column.' = ".preg_replace(\'/[^-.0-9]/\', \'\', $value)."';
				break;
/*
			case 'character varying':
				$where = 'LOWER('.$column.') = \'".strtolower(str_replace("\'", "\'\'", $value))."\'';
				break;
*/
			default:
				$where = $column.' = \'".str_replace("\'", "\'\'", $value)."\'';
				break;
		}

		$php = self::eol();
		$php .= self::tab().'static function getBy'.self::camelCase($column).'($value, $first_only = '.$first_only.', $include_deleted = false)'.self::eol();
		$php .= self::tab().'{'.self::eol();
		$php .= self::tab(2).'if (!$value || strlen($value) == 0)'.self::eol();
		$php .= self::tab(2).'{'.self::eol();
		$php .= self::tab(3).'return ($first_only ? null : []);'.self::eol();
		$php .= self::tab(2).'}'.self::eol();
		$php .= self::tab(2).'$class = get_called_class();'.self::eol();
		$php .= self::tab(2).'$object = new $class();'.self::eol();
		$php .= self::tab(2).'$sql = "SELECT * FROM $object->table WHERE '.$where.'";'.self::eol();
		$php .= self::tab(2).'return self::selectAll($sql, $first_only, $include_deleted);'.self::eol();
		$php .= self::tab().'}'.self::eol();

/*
		$php .= self::eol();
		$php .= self::tab().'static function getOneBy'.self::camelCase($column).'($value, $first_only = '.$first_only.', $include_deleted = false)'.self::eol();
		$php .= self::tab().'{'.self::eol();
		$php .= self::tab(2).'if (strlen($value) == 0)'.self::eol();
		$php .= self::tab(2).'{'.self::eol();
		$php .= self::tab(3).'return null;'.self::eol();
		$php .= self::tab(2).'}'.self::eol();
		$php .= self::tab(2).'$class = get_called_class();'.self::eol();
		$php .= self::tab(2).'$object = new $class();'.self::eol();
		$php .= self::tab(2).'$sql = "SELECT * FROM $object->table WHERE '.$where.'";'.self::eol();
		$php .= self::tab(2).'return self::selectOne($sql, $first_only, $include_deleted);'.self::eol();
		$php .= self::tab().'}'.self::eol();
*/

		return $php;
	}

	/**
	 * locate files no longer used
	 */
	static function findOldFiles()
	{
		$tables = self::getTables(false);

		$path = SLEDGEMC_PATH.'/models';

		$remove = [];

		$files = scandir($path.'/base');
		foreach ($files as $file)
		{
			if (substr($file, -8) == 'Base.php')
			{
				$class = substr($file, 0, -8);
				$table = self::unCamelCase($class);

				if (!isset($tables[$table]))
				{
					$remove[] = $class;
				}
			}
		}

		if (empty($remove))
		{
			echo "\r\nNo orphaned class files found\r\n";
		}
		else
		{
			echo "\r\n".count($remove)." orphaned classes found.\r\n\r\n";

			$files = [];
			foreach ($remove as $i => $class)
			{
				echo "Remove {$class} ? (y/n)\r\n";
				if (trim(fgets(STDIN)) != 'y')
				{
					unset($remove[$i]);
				}
				else
				{
					$files[] = "{$path}/base/{$class}Base.php";
					$files[] = "{$path}/{$class}.php";
				}
			}

			foreach ($files as $i => $file)
			{
				if (!file_exists($file))
				{
					unset($files[$i]);
				}
			}

			if (empty($files))
			{
				echo "\r\nNo files removed.\r\n";
			}
			else
			{
				echo "\r\nThe following files will be removed:\r\n";
				foreach ($files as $file)
				{
					echo "\r\n$file";
				}
				echo "\r\n\r\nAre you sure you want to delete these? (YES/NO)\r\n";
				if (trim(fgets(STDIN)) == 'YES')
				{
					foreach ($files as $file)
					{
						$cmd = "rm {$file}";
						echo "\r\n$cmd";
						shell_exec($cmd);
					}
					echo "\r\n\r\nDone.\r\n";
				}
				else
				{
					echo "\r\nNo files were removed.\r\n";
				}
			}
		}

		echo "\r\n";
	}	

	/**
	 * build a database from existing sledge classes
	 */
	static function buildFromFilesystem()
	{
		$path = SLEDGEMC_PATH.'/models/base';

		$sorted = [];
		$files = scandir($path);
		foreach ($files as $file)
		{
			if (substr($file, 0, 1) == '.')
			{
				continue;
			}
			if (substr($file, -4) == '.php')
			{
				$sorted[strlen($file)][] = substr($file, 0, -4);
			}
		}
		ksort($sorted);

		foreach ($sorted as $length => $classes)
		{
			foreach ($classes as $class)
			{
				$base_table = SLEDGEMC_BASE_TABLE;
				$base_table_child = SLEDGEMC_CHILD_TABLE;

				$object = new $class();
				if (!$object instanceof BaseObject)
				{
					continue;
				}

				$parent = false;
				$table = $object::TABLE_NAME;
				if (!in_array($table, [$base_table, $base_table_value, $base_table_child]))
				{
					$parts = explode('_', $table);
					$first = array_pop($parts);
					if (!$parts)
					{
						$parts = [$first];
					}

					$parent_table = implode('_', $parts);
					$parentClass = str_replace(' ', '', deslugify($parent_table));
					if (!$parentClass)
					{
						$parentClass = SLEDGEMC_BASE_TABLE;
					}
					if (class_exists($parentClass))
					{
						$parent = new $parentClass();
						if ($parent instanceof BaseObject)
						{
							$inherit = $parent->getColumnTypes();
						}
					}
				}

				$columns = [];
				foreach ($object->getColumnTypes() as $column_name => $column_data)
				{
					if ($parent && isset($inherit[$column_name]))
					{
						continue;
					}

					if ($column_name == 'id')
					{
						$columns[] = "id bigserial";
					}
					else
					{
						$type = $column_data[0];
						switch ($type)
						{
							case 'json':
							case 'text':
							case 'integer':
							case 'smallint':
							case 'bigint':
							case 'timestamp':
								$columns[] = "{$column_name} {$type}";
								break;
							case 'character varying':
								$columns[] = "{$column_name} varchar({$column_data[1]})";
								break;
							default:
								echo "unknown type {$type} in {$class}\r\n";
								break;
						}
					}
				}
				$schema = "(".implode(', ', $columns).")";
				$inherits = ($parent ? "inherits ({$parent_table})" : "");

				$sql = "CREATE TABLE IF NOT EXISTS {$table} {$schema} {$inherits}";
				echo "$sql\r\n";
				self::query($sql);
			}
		}
	}
}
