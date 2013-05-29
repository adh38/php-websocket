DIM = 2

class Player
	constructor: (@id, x, y) ->
		@pos = []
		@pos[0] = x
		@pos[1] = y

$(document).ready ->

	log = (msg) -> $('#log').prepend("#{msg}<br />")	
	
	serverIP = $('meta[name="websocket-IP"]').attr('content')
	serverPort = $('meta[name="websocket-port"]').attr('content')
	log 'read IP = ' + serverIP + ', port = ' + serverPort
	window.serverUrl = 'ws://' + serverIP + ':' + serverPort + '/game'
	window.myID = -1
	window.keys = {}
	
	openSocket = ->

		if typeof window.players != 'undefined'
			window.players.length = 0
		else
			window.players = []
			
		if window.MozWebSocket		
			@socket = new MozWebSocket window.serverUrl
			log "Has MozWebSocket"
		else if window.WebSocket		
			@socket = new WebSocket window.serverUrl
			log "Has WebSocket"
		else
			log "No websockets!"
			return
		log "Connecting to " + window.serverUrl
		socket.binaryType = 'blob'
		socket.onopen = (msg) ->
			$('#status').removeClass().addClass('online').html('connected')
		socket.onmessage = (msg) ->
			str = msg.data
			log 'msg: ' + str
			match = /^([^=]+)=(.+)$/.exec(str)
			key = match[1]
			vals = match[2].split ','
#			log 'key = ' + key + ', vals = ' + match[2]
			if !key || !vals
				return
			switch key
				when 'id'
					id = parseInt(vals[0])
					if vals.length == 1
						log 'player ' + id + ' has left'
						delete window.players[id]
						break
					self = parseInt(vals[1])
					x = parseInt(vals[2])
					y = parseInt(vals[3])
					if !id
						log 'no player id given'
						return
					if typeof window.players != 'undefined' && typeof window.players[id] == 'Player'
						log 'already a player with ID ' + id
						return
					window.players[id] = new Player(id, x, y)
					if self
						window.myID = id
						log 'you are player ' + id
				when 'pos'
					id = parseInt(vals[0])
					window.players[id].pos[0] = vals[1]
					window.players[id].pos[1] = vals[2]
#					log 'player ' + id + ' now at ' + window.players[id].pos[0] + ',' + window.players[id].pos[1]
				when 'state'
					for val in vals
						if typeof val == 'undefined' || val == ''
							break
						log 'piece: ' + val
						arr1 = val.split '-'
						id = parseInt arr1[0]
						if typeof window.players[id] != 'undefined'
							pos = arr1[1].split ';'
							for dim in [0..DIM-1]
								window.players[id].pos[dim] = parseFloat pos[dim]
						else
							log id + ' is not a player'
			repaint()
		socket.onclose = (msg) ->
			log 'closed'
			$('#status').removeClass().addClass('offline').html('disconnected')
	
	closeSocket = ->
		@socket.close()
	
	openSocket()

	repaint = ->
		return if window.myID<0
		panel = document.getElementById 'gamepanel'
		if panel.getContext
#			log 'repainting ' + window.players.length + ' players'
			ctx = panel.getContext "2d"
			ctx.fillStyle = "rgb(0,0,0)"
			ctx.fillRect(0, 0, 400, 300)
			ctx.fillStyle = "rgb(0,255,0)"
			for player in window.players
				if typeof player != 'undefined'
#					log 'drawing player ' + player.id + ' at ' + player.pos[0] + ',' + player.pos[1]
					ctx.beginPath()
					ctx.arc(player.pos[0], player.pos[1], 10, 0, 2*Math.PI)
					ctx.fill()
		else
			log 'no context for gamepanel'
			
	$('#status').click ->
		switch window.socket.readyState
			when 0,2
				log 'socket busy'
			when 1
				log 'requested to close'
				closeSocket()
			when 3
				log 'requested to connect'
				openSocket()
	
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
	
	sendKeys = ->
		log 'sending keys'
		str='keys:'
		for key,val of window.keys
			if typeof key != 'undefined' && key != 'RUNNING'
				str = str + key + ','
		log 'sending ' + str
		console.log 'sending %o',window.keys
		socket.send str

	$(document).keydown (e) ->
		switch e.keyCode
			when 73
				data = 'down'
			when 74
				data = 'left'
			when 75
				data = 'up'
			when 76
				data = 'right'
		if data
#			if typeof window.keys[data] != 'undefined'
#				return
			log data
			window.keys[data] = true
#			if typeof window.keys['RUNNING'] == 'undefined'
#				@keyInt = setInterval sendKeys,35
#				log 'set interval'
#			window.keys['RUNNING'] = true
			sendKeys()

	$(document).keyup (e) ->
		switch e.keyCode
			when 73
				data = 'down'
			when 74
				data = 'left'
			when 75
				data = 'up'
			when 76
				data = 'right'
		if data
			delete window.keys[data]
#			found = false
#			for key,val of window.keys
#				if typeof key != 'undefined' && key != 'RUNNING'
#					found = true
#			if !found
#				delete window.keys['RUNNING']
#				clearInterval @keyInt
#				log 'cleared interval'
			sendKeys()

