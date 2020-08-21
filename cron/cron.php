<?php

require __DIR__.'/config.php';

if (WEB)
{
	$controller = new Controller();
	$controller->redirect('home');
}

if (imRunning())
{
        die("RUNNING");
        exit;
}
