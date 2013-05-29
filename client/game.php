<!DOCTYPE html>
<html>
<head>
	<?php
		//start up the game server if not done already by another client
		//require '../server/server.php';

		//get the server specs from the server_specs.txt file
		$serverIP = '';
		$serverPort = 8000;
		$serverFile = fopen('../server_specs.txt', 'r');
		if($serverFile) {
			while(($line = fgets($serverFile)) !== false) {
				$arr = explode("\t", $line);
				switch(strtoupper($arr[0])) {
					case 'IP': $serverIP = chop($arr[1],"\n");
						break;
					case 'PORT': $serverPort = chop($arr[1],"\n");
						break;
					default: break;
				}
			}
			fclose($serverFile);
			echo "<meta name=\"websocket-IP\" content=\"" . $serverIP . "\">\n";
			echo "\t<meta name=\"websocket-port\" content=\"" . $serverPort . "\">\n";
		}
	?>

	<link rel="stylesheet" href="css/game.css">
	
    <script src="js/jquery.min.js"></script>
	<script src="js/json2.js"></script>
	<script src="lib/coffeescript/jsmaker.php?f=game.coffee"></script>
    
	<meta charset=utf-8 />

	<title>Game</title>
</head>
<body>
    <div id="container">
        <h1>Game</h1>
		<span id="status" class="offline">not started</span>
		
		<p>
		<canvas id="gamepanel" width="400" height="300" style="border: 1px solid black"></canvas>
		
		<h2>Server Comm Log</h2>
        <div id="log"></div>
    </div>
</body>
</html>​
