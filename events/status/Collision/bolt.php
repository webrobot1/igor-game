<?php

// укажем что должно произойти если столкнулись
// вот так элегантно можно оправлять строковые версии кода которые после превратятся в замыкания (мы в php вызовем eval  в событии onCollision)
// и на постоянные события (что будут в бд писаться)  пригодиться что бы сохранять (тк уже созданное Closure и не в бд не передать не в другую локацию)
	
World::filter($object->position, static function(EntityAbstract $target) use($object):bool
{
	if(
		$target->components->isset("hp") 
			&& 
		$target->components->get("hp")>0 
			&&
		$target->key != $object->components->get("owner")
	) 
	{
		$target->events->add("status/hurt", "index", ["from"=>$object->components->get("owner"), "hp"=>1]);
		$object->events->remove("move/walk");
		
		// отложенно удалим наш объект что бы он успел пролететь до цели
		$object->position = $target->position;				// и сразу установим позицию точную попадания
		$object->remove();									// уничтожим наш фаербол тк было столкновение на следующем кадре

		return true;
	}
	else
		return false;

}, $data['distance'], $data['count']);