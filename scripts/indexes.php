<?php

require 'config.php';

$indexes = [];
$indexes['sessions'][] = ['session_key'];
$indexes['game_user'][] = ['email'];

$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name";
$tables = selectAll($sql);
foreach ($tables as $table)
{
        $table = $table['table_name'];

        $indexes[$table][] = ['id'];
        $indexes[$table][] = ['parent_id'];
        $indexes[$table][] = ['id','parent_id'];
        $indexes[$table][] = ['id','deleted'];
	$indexes[$table][] = ['parent_id','deleted'];
	$indexes[$table][] = ['parent_id','name'];
        $indexes[$table][] = ['name'];

	if (substr($table, 0, 4) == 'game' || $table == 'map')
	{
		$indexes[$table][] = ['z','y','x'];
	}
}

foreach ($indexes as $table => $table_indexes)
{
        foreach ($table_indexes as $index_columns)
        {
                $index = $table.'_'.implode('_', $index_columns).'_index';
                $columns = implode(',', $index_columns);

                $sql = "CREATE INDEX IF NOT EXISTS {$index} ON {$table} ({$columns})";
		echo "$sql;\r\n";
        }
}
