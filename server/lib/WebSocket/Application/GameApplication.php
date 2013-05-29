<?php

namespace WebSocket\Application;

include_once 'Misc.php';

/**
 * Websocket-Server demo and test application.
 * 
 * @author Simon Samtleben <web@lemmingzshadow.net>
 */
class GameApplication extends Application
{
    private $_clients = array();
	private $_clientdata = array();
	private $_numPlayers = 0;
	private $_laststate = '';
	private $_filename = '';
	private $_width = 400, $_height = 300;

	public function onConnect($client)
    {
		$id = $client->getClientId();
        $this->_clients[$id] = $client;
        $newid = 1;
        //double-check the current number of players and use the first available ID
        $this->_numPlayers = 0;
        foreach($this->_clientdata as $player)
        {
        	$this->_numPlayers++;
        	if($player->getId() == $newid) $newid++;
        }
		$this->_numPlayers++;
		if($this->_numPlayers == 1) {
			$x = $this->_width / 6;
			$y = $this->_height / 2;
		} else {
			$x = $this->_width*5 / 6;
			$y = $this->_height / 2;
		}
		$this->_clientdata[$id] = new Player($newid, $x, $y);

		//broadcast the new player - tell the new client this player is hers, and tell her about all the other players
		foreach($this->_clients as $cli)
		{
			if($cli === $client) continue;
			$cli->send('id='.$newid.',0,'.$x.','.$y);
		}
		foreach($this->_clientdata as $player)
		{
			$hers = ($player->getId() == $newid) ? 1 : 0;
			$client->send('id='.$player->getId().','.$hers.','.$player->getX().','.$player->getY());
		}
		
		//now that we have at least one player, start the frame loop
		
    }

    public function onDisconnect($client)
    {
        $id = $client->getClientId();		
		unset($this->_clients[$id]);
		//broadcast this player's departure
		$player = $this->_clientdata[$id];
		foreach($this->_clients as $cli)
		{
			$cli->send('id='.$player->getId());
		}
		unset($this->_clientdata[$id]);
    }

    public function onData($data, $client)
    {
    	$speed = 5;
		$player = $this->_clientdata[$client->getClientId()];
		$fields = explode(':', $data);
		if($fields[0] !== 'keys') return;
		$arr = explode(',', $fields[1]);
		$vel = array();
		for($i = 0; $i < DIM; $i++) $vel[$i] = 0;
		
		foreach($arr as $key) {
			switch($key) {
				case 'up':
					$vel[1] += PLAYERVEL;
					break;
				case 'down':
					$vel[1] -= PLAYERVEL;
					break;
				case 'left':
					$vel[0] -= PLAYERVEL;
					break;
				case 'right':
					$vel[0] += PLAYERVEL;
					break;
			}
		}
		
		for($i = 0; $i < DIM; $i++) $player->setVelDim($i, $vel[$i]);
		
/*		foreach($this->_clients as $cli) {
			$cli->send('pos='.$player->getId().",".$player->getX().",".$player->getY());
		}//*/
    }
	
	public function onBinaryData($data, $client)
	{
		$client->send($this->_encodeData('echo', 'no binary allowed yet'));
	}
	
	public function frame() {
		//move all players
		$playerinfo = 'state=';
		foreach($this->_clients as $client) {
			$id = $client->getClientId();
			$player = $this->_clientdata[$id];
			$player->move();
			$playerinfo .= $player->getId() . '-' . $player->posstr(';',false) . ',';
		}
		//only broadcast the state if it has changed
		if($this->_laststate === $playerinfo) return;
		$this->_laststate = $playerinfo;
		//broadcast current state
		foreach($this->_clients as $client) {
			$client->send($playerinfo);
		}
	}

}
