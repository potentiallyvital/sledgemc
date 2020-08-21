<?php

$db_type = (!empty($argv[1]) ? $argv[1] : 'postgres');
if (!in_array($db_type, ['postgres','mysql']))
{
	die("\r\nusage : php run_hammer.php <postgres/mysql>\r\n");
}

$skip_auto_include = true;
require 'config.php';
require 'SledgeMCHammer_'.$db_type.'.php';

SledgeMCHammer::build();
