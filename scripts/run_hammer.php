<?php

require __DIR__.'/config.php';

echo "\r\n";
echo "=====================================\r\n";

SledgeMC::buildFromDatabase();

echo "=====================================\r\n";

echo "\r\nExporting schema and doing git add...";
if (!is_dir(SLEDGEMC_PATH.'/.server'))
{
	mkdir(SLEDGEMC_PATH.'/.server');
}
shell_exec("pg_dump -s ".SLEDGEMC_NAME." > ".SLEDGEMC_PATH."/.server/schema.sql");
shell_exec("git add ".SLEDGEMC_PATH."/.server/schema.sql ".SLEDGEMC_PATH."/models/base/*");

echo "\r\nChecking indexes...\r\n";
echo shell_exec("php ".__DIR__."/indexes.php");

echo "DONE\r\n";
