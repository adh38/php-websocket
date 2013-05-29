$(document).ready ->
	log = (msg) -> $('#log').prepend("#{msg}<br />")	

	serverIP = $('meta[name="websocket-IP"]').attr('content')
	serverPort = $('meta[name="websocket-port"]').attr('content')
	log 'read IP = ' + serverIP + ', port = ' + serverPort
	serverUrl = 'ws://' + serverIP + ':' + serverPort + '/status'

	if window.MozWebSocket		
		socket = new MozWebSocket serverUrl
		log "Has MozWebSocket"
	else if window.WebSocket		
		socket = new WebSocket serverUrl
		log "Has WebSocket"
	else log "No websockets!"

	log "Connecting to " + serverUrl
	log "ready = " + socket.readyState
	log "buff = " + socket.bufferedAmount;

	socket.onopen = (msg) ->
		$('#status').removeClass().addClass('online').html('connected')
		$('#serverPanel button').html('Stop Server')

	socket.onmessage = (msg) ->
		response = JSON.parse(msg.data)
		switch response.action
			when "statusMsg"			then statusMsg response.data
			when "clientConnected"		then clientConnected response.data
			when "clientDisconnected"	then clientDisconnected response.data			
			when "clientActivity"		then clientActivity response.data
			when "serverInfo"			then refreshServerinfo response.data

	socket.onerror = (msg) ->
		console.log('error: %o', msg);

	socket.onclose = (msg) ->
		log 'closed'
		$('#status').removeClass().addClass('offline').html('disconnected')
		$('#serverPanel button').html('Start Server')

	$('#status').click ->
		socket.close()

	statusMsg = (msgData) ->
		switch msgData.type
			when "info" then log msgData.text
			when "warning" then log "<span class=\"warning\">#{msgData.text}</span>"

	clientConnected = (data) ->		
		$('#clientListSelect').append(new Option("#{data.ip}:#{data.port}", data.port))
		$('#clientCount').text(data.clientCount)

	clientDisconnected = (data) ->
		$("#clientListSelect option[value='#{data.port}']").remove()
		$('#clientCount').text(data.clientCount)

	refreshServerinfo = (serverinfo) ->	
		$('#clientCount').text(serverinfo.clientCount)
		$('#maxClients').text(serverinfo.maxClients)
		$('#maxConnections').text(serverinfo.maxConnectionsPerIp)
		$('#maxRequetsPerMinute').text(serverinfo.maxRequetsPerMinute)
		for port, ip of serverinfo.clients			
			$('#clientListSelect').append(new Option(ip + ':' + port, port));	

	clientActivity = (port) ->
		$("#clientListSelect option[value='#{port}']").css("color", "red").animate({opacity: 100}, 600, ->
			$(this).css("color", "black")
		)
