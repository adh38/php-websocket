<?php
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

echo 'hello';

ini_set('display_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/lib/SplClassLoader.php');

$classLoader = new SplClassLoader('WebSocket', __DIR__ . '/lib');
$classLoader->register();

//open log file for debug output
echo 'running as ' . exec('whoami') . PHP_EOL;
$logDir = __DIR__ . '/server_log/';
//$logDir = '/srv/disk12/1380903/www/simsandgames.co.nf/php-websocket/server/server_log/';
//$logDir = '/var/www/html/php-websocket/server/server_log/';
if(!file_exists($logDir)) mkdir($logDir, 0755);
$logFile = fopen($logDir . 'server_log.txt', 'a+');
fclose($logFile);
?>
