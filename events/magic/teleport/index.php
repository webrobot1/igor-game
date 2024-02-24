<?php

$to = (new Position($data['x'], $data['y'], $data['z']));

if($object->position->tile() == $to->tile())
	throw new Exception('клетка '.$to.' равна округленной позиции игрока (слишком маленькая дистанция телепорта)');

if(empty($info["map_id"]) && empty(Map2D::getCurrentMapTiles()[$to->tile()]))
	throw new Exception('позиция '.(string)$to.' непроходима на текущей карте');	

if(!empty($data['map_id']))
	$object->map_id = $data['map_id'];	

$object->position = $to;