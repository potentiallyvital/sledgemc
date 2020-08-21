<?php

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);

$force_includes = false;

$http = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http');

$definitions = [
        'sledgemc_app' => 'Short App Name',
        'sledgemc_title' => 'App HTML Title',
        'sledgemc_host' => 'localhost',
        'sledgemc_user' => 'username',
        'sledgemc_pass' => 'password',
        'sledgemc_name' => 'database_name',
        'sledgemc_path' => '/path/to/public_html',
        'sledgemc_master_user' => 'admin@yourdomain.com',
        'base_url' => $http.'://www.yourdomain.com',
        'web' => (PHP_SAPI !== 'cli'),
        'dev' => true,
];

foreach ($definitions as $key => $value)
{
        $key = strtoupper($key);
        if (!defined($key))
        {
                define($key, $value);
        }
}

if (empty($skip_auto_include))
{
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
                                        if ($force_includes)
                                        {
                                                require_once $file;
                                        }
                                        else
                                        {
                                                include_once $file;
                                        }
                                }
                        }
                }
                elseif ($force_includes)
                {
                        require_once $inc_file;
                }
                else
                {
                        include_once $inc_file;
                }
        }
}

spl_autoload_register('load_models');

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
