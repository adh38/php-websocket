<?php

namespace WebSocket\Application;

include_once 'Misc.php';

class Player
{
	private $_pos = array();
	private $_vel = array();
	private $_id;
	
	public function __construct($num)
	{
		$this->_id = $num;
		$poslen = func_num_args() - 1;
		for($i = 0; $i < DIM; $i++) {
			if($i < $poslen) $this->_pos[$i] = func_get_arg($i+1);
			else $this->_pos[$i] = 0;
			$this->_vel[$i] = 0;
		}
	}
	
	public function getId() 
	{
		return $this->_id;
	}
	
	public function getX()
	{
		return $this->_pos[0];
	}
	
	public function getY()
	{
		return $this->_pos[1];
	}
	
	public function moveTo()
	{
		$narg = func_num_args();
		for($i = 0; $i < $narg and $i < DIM; $i++)
			$this->_pos[$i] = func_get_arg($i);
	}
	
	public function moveBy()
	{
		$narg = func_num_args();
		for($i = 0; $i < $narg and $i < DIM; $i++)
			$this->_pos[$i] += func_get_arg($i);
	}
	
	public function moveDim($dim, $dx)
	{
		if($dim < DIM) $this->_pos[$dim] += $dx;
	}
	
	public function move()
	{
		for($i = 0; $i < DIM; $i++)
			$this->_pos[$i] += $this->_vel[$i];
	}
	
	public function setVelDim($dim, $vel)
	{
		if($dim < DIM) $this->_vel[$dim] = $vel;
	}
	
	public function posstr($delim = ', ', $paren = true)
	{
		$str = '';
		if($paren) $str .= '(';
		for($i = 0; $i < DIM-1; $i++) $str .= $this->_pos[$i] . $delim;
		$str .= $this->_pos[DIM-1];
		if($paren) $str .= ')';
		return $str;
	}
}
