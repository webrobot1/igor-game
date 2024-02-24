<?php

// что бы не тратить постянно время на расчеты
$info = $object->components->get('position');

if(!isset($info['x']) || !isset($info['x']))
	throw new Exception('отсутвует позиция к которой требуется переместится');

$position = new Position($info['x'], $info['y'], ($info['z']??0));
if($object->position->tile() == $to->tile())
	throw new Exception('клетка '.$to.' равна округленной позиции игрока (слишком маленькая дистанция у портала)');

// если карта далекая и координат нет ощибки не будет но и игрок не застрянет - переместиться в свободную клетку
if(empty($info["map_id"]) && empty(Map2D::getCurrentMapTiles()[$position->tile()]))
	throw new Exception('позиция '.(string)$position.' непроходима на текущей карте');	


$data = array();
if(!empty($info["sort"]))
	$data['sort'] = $info["sort"];
if(!empty($info["z"]))
	$data['z'] = $info["z"];
if(!empty($info["x"]))
	$data['x'] = $info["x"];
if(!empty($info["y"]))
	$data['y'] = $info["y"];

if(!empty($info["map_id"]))
	$data['map_id'] = $info["map_id"];


// укажем какие объекты надо обрабатвать для столкновений
$object->events->add('status/collision', 'portal', ['data'=>$data]);
