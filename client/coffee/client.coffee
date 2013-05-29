$(document).ready ->
	log = (msg) -> $('#log').prepend("#{msg}<br />")	
	serverUrl = 'ws://localhost:7622/demo'
	if window.MozWebSocket		
		socket = new MozWebSocket serverUrl
		log "Has MozWebSocket"
	else if window.WebSocket		
		socket = new WebSocket serverUrl
		log "Has WebSocket"
	else log "No websockets!"

	socket.binaryType = 'blob'

	log "Connecting to " + serverUrl
	log "ready = " + socket.readyState
	log "buff = " + socket.bufferedAmount;

	socket.onopen = (msg) ->
		$('#status').removeClass().addClass('online').html('connected')
	
	socket.onmessage = (msg) ->
		response = JSON.parse(msg.data)
		log("Action: #{response.action}")
		log("Data: #{response.data}")
	
	socket.onclose = (msg) ->
		log 'closed'
		$('#status').removeClass().addClass('offline').html('disconnected')
	
	$('#status').click ->
		socket.close()
	
	$('#send').click ->
		payload = new Object()
		payload.action = $('#action').val()
		payload.data = $('#data').val()
		socket.send(JSON.stringify(payload))
		
	$('#sendfile').click ->
		data = document.binaryFrame.file.files[0]
		if data			
			socket.send(data)
		return false
		
