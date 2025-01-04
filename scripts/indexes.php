<?php
require __DIR__.'/config.php';

$table = (isset($argv[1]) ? $argv[1] : null);
$columns = $argv;
array_shift($columns);
array_shift($columns);

$indexes = [];
if ($table)
{
	// make specific indexes specified in args
	$tables = [['table_name'=>$table]];

	if ($columns)
	{
		$indexes[$table][] = $columns;
	}
}
else
{
	// load up all tables
	$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name";
	$tables = selectAll($sql);
}

// make default indexes
foreach ($tables as $table)
{
	$table = $table['table_name'];

	$indexes[$table][] = ['id'];

	switch ($table)
	{
		case 'children':
			$indexes[$table][] = ['parent_id'];
			$indexes[$table][] = ['child_id'];
			$indexes[$table][] = ['parent_id','child_id'];
			$indexes[$table][] = ['parent_id','child_class'];
			break;
		case 'login':
			$indexes[$table][] = ['email'];
			break;
		case 'role':
			$indexes[$table][] = ['name'];
			$indexes[$table][] = ['code'];
			break;
	}
}

// ignore existing indexes
$existing = [];
foreach (selectAll("SELECT * FROM pg_indexes WHERE schemaname = 'public'") as $row)
{
	$table = $row['tablename'];

	$cols = $row['indexdef'];
	$cols = explode('(', $cols);
	$cols = array_pop($cols);
	$cols = explode(')', $cols);
	$cols = array_shift($cols);
	$cols = explode(',', $cols);
	$cols = array_map('trim', $cols);

	$columns = implode(',', $cols);
	$index = 'idx_'.md5($table.'-'.$columns);

	if ($row['indexname'] != $index)
	{
		execute("ALTER INDEX {$row['indexname']} RENAME TO {$index}");
	}

	$existing[$index] = $row;
}

// make em
$made = false;
foreach ($indexes as $table => $col_indexes)
{
	foreach ($col_indexes as $cols)
	{
		$columns = implode(',', $cols);
		$index = 'idx_'.md5($table.'-'.$columns);

		if (empty($existing[$index]))
		{
			$sql = "CREATE INDEX IF NOT EXISTS {$index} ON {$table} ({$columns})";
			echo $sql.";\r\n";
			execute($sql);

			$made = true;
		}
	}
}

if (!$made)
{
	echo "Indexes look good!\r\n";
}
