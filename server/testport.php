<?php

echo 'hello<br/>';

$serverIP = '';
$serverPort = 8000;
$serverFile = fopen('../server_specs.txt', 'r');
if(!$serverFile) return;
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
fclose($serverFile);

echo 'testing ' . $serverIP . ':' . $serverPort . '<br/>';
$sock = fsockopen('82.197.130.123', $serverPort, $errnum, $errstr, 1);
if($sock) {
	echo 'port is open<br/>';
	fclose($sock);
}
else echo 'port is closed<br/>';
//*/
?>
