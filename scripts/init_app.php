<?php
require __DIR__.'/config.php';

echo "Enter new password for ".SLEDGEMC_EMAIL.":\r\n";
shell_exec('stty -echo');
$password = trim(fgets(STDIN, 4096));
shell_exec('stty echo');

echo "Confirm password for ".SLEDGEMC_EMAIL.":\r\n";
shell_exec('stty -echo');
$confirm = trim(fgets(STDIN, 4096));
shell_exec('stty echo');

if ($password != $confirm)
{
	echo "\r\nPASSWORD MISMATCH, ABORT\r\n\r\n";
	exit;
}

execute("TRUNCATE id");
execute("ALTER SEQUENCE id_sequence RESTART WITH 1");

$roles = ['admin','manager','user'];
foreach ($roles as $code)
{
	$role = Role::getByCode($code);
	if (!$role)
	{
		$role = new Role();
		$role->setName(ucwords($code));
		$role->setCode($code);
		$role->save();
	}
}

$account = new Account();
$account->setRoleId(1);
$account->setEmail(SLEDGEMC_EMAIL);
$account->save();

$login = new Login();
$login->setEmail(SLEDGEMC_EMAIL);
$login->setPassword($password);
$login->save();
$login->linkTo($account);

