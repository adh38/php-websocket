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

//open log file for debug output
echo 'running as ' . exec('whoami') . '</br>' . PHP_EOL;
$logDir = __DIR__ . '/server_log/';
//$logDir = '/srv/disk12/1380903/www/simsandgames.co.nf/php-websocket/server/server_log/';
//$logDir = '/var/www/html/php-websocket/server/server_log/';
if(!file_exists($logDir)) mkdir($logDir, 0755);
$logFile = fopen($logDir . 'server_log.txt', 'a+');
function serverLog($str) {
	global $logFile;
	fwrite($logFile, $str);
	echo '<br/>' . $str;
}
function stampLog($str) {
	serverLog(date('Y-m-d H:i:s') . ' [info] ' . $str . PHP_EOL);
}

//get server URL from spec file
stampLog('getting server specs');
$serverIP = '';
$serverPort = 8000;
$serverFile = fopen('../server_specs.txt', 'r') or (log("couldn't open server file") and die);
while(($line = fgets($serverFile)) !== false) {
	$arr = explode("\t", $line);
	switch(strtoupper($arr[0])) {
		case 'IP': $serverIP = chop($arr[1], "\n");
			break;
		case 'PORT': $serverPort = chop($arr[1], "\n");
			break;
		default: break;
	}
}
stampLog('IP = ' . $serverIP . ', port = ' . $serverPort);
fclose($serverFile);
if(!isset($serverIP, $serverPort)) {
	stampLog('Invalid server specs');
	fclose($logFile);
	return;
}

//see if we can open the requested port to network traffic
//$ret = exec('netstat -nap | grep 7623'); //' . $serverPort);
//$ret = exec('iptables -A INPUT -p tcp --dport ' . $serverPort . ' -j ACCEPT');
//stampLog('iptables: ' . $ret);

//make sure the port is not already open
/*if(@fsockopen($serverIP, $serverPort, $errno, $errstr, 5)) {
	stampLog('Port ' . $serverPort . ' on server ' . $serverIP . ' is already open');
	fclose($logFile);
	return;
}//*/
$server = new \WebSocket\Server($serverIP, $serverPort, false);
if(!$server->server_created()) {
	stampLog("Couldn't instantiate server on " . $serverIP . ':' . $serverPort);
	return;
}
stampLog('made server');

// server settings:
$server->setMaxClients(100);
$server->setCheckOrigin(false);
$server->setMaxConnectionsPerIp(100);
$server->setMaxRequestsPerMinute(2000);

// Hint: Status application should not be removed as it displays usefull server informations:
$server->registerApplication('status', \WebSocket\Application\StatusApplication::getInstance());
$server->registerApplication('demo', \WebSocket\Application\DemoApplication::getInstance());
$server->registerApplication('game', \WebSocket\Application\GameApplication::getInstance());

stampLog('running server');
$server->run();
fclose($logFile);

?>
