<!DOCTYPE html>
<html>
<head>
	<?php //first get the server specs from the server_specs.txt file
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

	<link rel="stylesheet" href="css/status.css">
	
    <script src="js/jquery.min.js"></script>
	<script src="js/json2.js"></script>
	<script src="lib/coffeescript/jsmaker.php?f=status.coffee"></script>
    
	<meta charset=utf-8 />

	<title>Shiny WSS Status</title>
</head>
<body>
    <div id="container">
        <h1>Shiny WSS Status</h1>
		<span id="status" class="offline">not started</span>
		
		<div id="main">
			<div id="clientList">
				<h2>Clients:</h2>
				<select id="clientListSelect" multiple="multiple"></select>
			</div>

			<div id="serverInfo">
				<h2>Server Info:</h2>
				<p>Connected Clients: <span id="clientCount"></span></p>
				<p>Limit Clients: <span id="maxClients"></span></p>
				<p>Limit Connections/IP: <span id="maxConnections"></span></p>
				<p>Limit Requetes/Min: <span id="maxRequetsPerMinute"></span></p>
			</div>
			
			<div id="serverPanel">
				<form action="../server/server.php">
					<button type="submit">Start Server</button>
				</form>
			</div>
			
			<div class="clearer"></div>
			
			<div id="console">
				<h2>Server Messages:</h2>
				<div id="log"></div>
			</div>
		</div>			
    </div>
</body>
</html>​
