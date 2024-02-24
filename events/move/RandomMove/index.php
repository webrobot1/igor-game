<?php

if(!$object->events->get('fight/attack')->action && !$object->events->get('move/walk')->action && (!$object->components->isset('hp') || $object->components->get('hp')>0))
{
	$forward = null;
	$count = 0;
	
	// если есть куда идти
	if($tiles = Map2D::getTile($object->position->tile()))
	{
		// идем в направлении случайно доступной клетки
		$new_position = new Position(...explode(Position::DELIMETR, array_keys($tiles)[rand(0, count($tiles)-1)]));
		
		$forward = $object->position->forward($new_position);	
		$object->events->add('move/walk', 'index', ['x'=>$forward->x, 'y'=>$forward->y]);
	}
	
	// всеравно вешаем событие даже если ходить сейчас некуда (в заблокированной со всех сторон клетке) может потом будет
	// ходит с разными интервалами
	$object->events->get('move/randommove')->resetTime(rand(1, 3) * $object->events->get('move/randommove')->timeout);
}							