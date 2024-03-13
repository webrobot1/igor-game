<?php

if(!$object->events->get('fight/attack')->action && !$object->events->get('move/walk')->action && $object->components->isset('hp') && $object->components->get('hp')>0) 
{
	// не нужно передавать в use целый объект тк он может уже и удалиться с карты а у нас сслка на него тут и создается утечка памяти
	$key = $object->key;
	
	// каждый раз запрашиваем позицию объхектов (ниже двигаться могут) и сами id объектов
	// todo если есть признак attack_to првоерим не рядом ли игрок и жив ли он, что бы других не искать
	if(
		$gameObject = World::filter
		(
			$object->position
				,
			function(EntityAbstract $gameObject) use($key):bool
			{ 
				if(
					(($gameObject instanceOf Players) || !rand(0, 10))
						&& 
					$gameObject->components->isset('hp') 
						&& 
					$gameObject->components->get('hp')>0 
						&& 
					(!World::isset($key) || $gameObject->position->tile() != World::get($key)->position->tile())				// в теории могут существа оказаться на одной клетке , но так не атакуем а то вектор направления атаки будет нулевой и бцдет ошибка/ глобально в filter не надо проверять тк иногда надо проверить что существо стоит на объекте
				)
					return true;
				else
					return false;
			}
				, 
			($object->lifeRadius>0?$object->lifeRadius:10)
		)
	)
	{
		$gameObject = end($gameObject);
		$object->events->add('fight/attack', 'index', ['target'=>$gameObject->key]);	
	}
}