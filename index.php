<?php

if (empty($_SESSION))
{
        session_start();
}

require 'config.php';

if (!empty($_SERVER['HTTP_HOST']))
{
	$domain = explode('//', BASE_URL);
	$domain = array_pop($domain);

	if ($_SERVER['HTTP_HOST'] != $domain)
	{
		header('Location: '.BASE_URL);
	}
}

$controller = new BaseController();
$controller->initialize($_SERVER['REQUEST_URI']);
