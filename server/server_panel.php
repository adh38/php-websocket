<!DOCTYPE html>
<html>
	<head>
	<?php
	/* This program is free software. It comes without any warranty, to
	 * the extent permitted by applicable law. You can redistribute it
	 * and/or modify it under the terms of the Do What The Fuck You Want
	 * To Public License, Version 2, as published by Sam Hocevar. See
	 * http://sam.zoy.org/wtfpl/COPYING for more details. */

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	require(__DIR__ . '/lib/SplClassLoader.php');

	$classLoader = new SplClassLoader('WebSocket', __DIR__ . '/lib');
	$classLoader->register();

	$serverIP = '';
	$serverPort = 8000;
	$serverFile = fopen('../server_specs.txt', 'r') or die;
	while(($line = fgets($serverFile)) !== false) {
		$arr = explode("\t", $line);
		switch(strtoupper($arr[0])) {
			case 'IP': $serverIP = $arr[1];
				break;
			case 'PORT': $serverPort = $arr[1];
				break;
			default: break;
		}
	}
	fclose($serverFile);
	if($serverIP == 0 || $serverPort == 0) {
		echo 'Invalid server specs';
		die;
	}
	$server = new \WebSocket\Server($serverIP, $serverPort, false);

	// server settings:
	$server->setMaxClients(100);
	$server->setCheckOrigin(false);
	$server->setMaxConnectionsPerIp(100);
	$server->setMaxRequestsPerMinute(2000);

	// Hint: Status application should not be removed as it displays usefull server informations:
	$server->registerApplication('status', \WebSocket\Application\StatusApplication::getInstance());
	$server->registerApplication('demo', \WebSocket\Application\DemoApplication::getInstance());
	$server->registerApplication('game', \WebSocket\Application\GameApplication::getInstance());

	$server->run();
