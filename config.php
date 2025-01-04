<?php

$http = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http');
$domain = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'yourdomain.com');

$definitions = [
	// code
	'SLEDGEMC_APP' => 'My First App',
	'SLEDGEMC_TITLE' => 'Hello, world!',
	'SLEDGEMC_PATH' => '/var/www/public_html',
	'SLEDGEMC_EMAIL' => 'admin@yourdomain.com',

	// database config
	'SLEDGEMC_HOST' => 'localhost', // database host
	'SLEDGEMC_USER' => '', // database user
	'SLEDGEMC_PASS' => '', // database user password
	'SLEDGEMC_NAME' => '', // database name

	// database tables
	'SLEDGEMC_BASE_TABLE' => 'id',
	'SLEDGEMC_CHILD_TABLE' => 'children',
	'SLEDGEMC_ACCOUNT_TABLE' => 'account',
	'SLEDGEMC_ROLE_TABLE' => 'role',
	'SLEDGEMC_LOGIN_TABLE' => 'login',
	'SLEDGEMC_MESSAGE_TABLE' => 'message',
	'SLEDGEMC_FLASH_TABLE' => 'flash',

	// environment
	'BASE_URL' => $http.'://'.$domain,
	'DEV' => true,
	'WEB' => (PHP_SAPI !== 'cli'),
];

foreach ($definitions as $key => $value)
{
	if (!defined($key))
	{
		define($key, $value);
	}
}

if (DEV)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
else
{
	error_reporting(false);
}

$auto_include = [
	SLEDGEMC_PATH.'/libraries/',
	SLEDGEMC_PATH.'/controllers/BaseController.php',
	SLEDGEMC_PATH.'/controllers/Controller.php',
	SLEDGEMC_PATH.'/models/base/BaseObject.php',
	SLEDGEMC_PATH.'/models/base/GettersAndSetters.php',
];

foreach ($auto_include as $inc_file)
{
	if (substr($inc_file, -1) == '/')
	{
		$files = scandir($inc_file);
		foreach ($files as $inc_file_2)
		{
			$file = $inc_file.$inc_file_2;
			if (is_file($file) && substr($inc_file_2, 0, 1) != '.')
			{
				include_once $file;
			}
		}
	}
	else
	{
		include_once $inc_file;
	}
}

if (!function_exists('load_models'))
{
	function load_models($class)
	{
		if (substr($class, -10) == 'Controller')
		{
			if (file_exists(SLEDGEMC_PATH.'/controllers/'.substr($class, 0, -10).'.php'))
			{
				include_once SLEDGEMC_PATH.'/controllers/'.substr($class, 0, -10).'.php';
				return;
			}
		}
		else
		{
			$directories = [
				SLEDGEMC_PATH.'/models/',
				SLEDGEMC_PATH.'/models/base/',
			];

			foreach ($directories as $dir)
			{
				if (file_exists($dir.$class.'.php'))
				{
					include_once $dir.$class.'.php';
					return;
				}
			}
		}
	}

	spl_autoload_register('load_models');
}

/****************************
 *
 * HANDLING ERRORS
 *
 */
if (!function_exists('handle_errors'))
{
	function handle_errors()
	{
		$error = error_get_last();

		$types = [
			E_PARSE => 'PARSE ERROR',
			E_ERROR => 'FATAL ERROR',
			E_WARNING => 'WARNING',
			E_NOTICE => 'NOTICE',
		];
		$type = (!empty($error['type']) ? $error['type'] : 0);
		if (!isset($types[$type]))
		{
			return;
		}
		$type = $types[$type];

		$backtrace = [];
		$lines = explode(PHP_EOL, $error['message']);

		$message = array_shift($lines);

		foreach ($lines as $line)
		{
			$line = trim($line);
			if (!in_array($line, ['thrown','Stack trace:']) && !stristr($line, '{main}'))
			{
				$backtrace[] = $line;
			}
		}

		if (WEB)
		{
			$controller = Controller::getInstance();

			$controller->data['error_type'] = $type;

			$controller->data['error_info'] = [
				'message' => $message,
				'file' => $error['file'],
				'line' => $error['line'],
				'backtrace' => $backtrace,
			];

			$controller->view('main/error');
		}
		else
		{
			echo "\r\n+----- {$type} -------------------------------------------------------";
			echo "\r\n|";
			echo "\r\n| {$message}";
			echo "\r\n|";
			echo "\r\n| {$error['file']}";
			echo "\r\n| line {$error['line']}";
			echo "\r\n|";
			echo "\r\n| BACKTRACE:";
			echo "\r\n| -- {$error['file']}({$error['line']}): {$type}";
			foreach ($backtrace as $line)
			{
				$line = trim($line);
				if (!in_array($line, ['thrown','Stack trace:']))
				{
					echo "\r\n| {$line}";
				}
			}
			echo "\r\n+-------------------------------------------------------------------------\r\n\r\n";
			exit;
		}
	}

	register_shutdown_function('handle_errors');
	set_error_handler(handle_errors());
}
if (!function_exists('allow_dump'))
{
	function allow_dump()
	{
		// server always good
		if (!WEB)
		{
			return true;
		}

		// whitelisted IPs
		$allowed_ips = [
			'156.47.137.71', // nate home
		];
		if (!empty($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $allowed_ips))
		{
			return true;
		}

		// session/get vars
		$allow_keys = [
			'nate',
		];
		foreach ($allow_keys as $allow_key)
		{
			if (!empty($_GET[$allow_key]))
			{
				$_SESSION['ALLOW_DUMP'] = ($_GET[$allow_key] == 'true');
			}
		}
		if (!empty($_SESSION['ALLOW_DUMP']))
		{
			return true;
		}
	}
}
