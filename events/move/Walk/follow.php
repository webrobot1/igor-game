<?php
	// если движение инициализировал игрок сбросим атаку и фаерболт который вызвает так же и движение
	if($from_client)
	{
		$object->events->remove('fight/bolt');
		$object->events->remove('fight/attack');
	}
	
	if($target != $object->key && World::isset($target))
	{
		$follow = World::get($data['target']);
		$distance = $object->position->distance($follow);
		
		// если  объект дальше клетки - идем за ним
		if($distance>STEP)
		{
			//пройдемся к позиции объекта, сформируем поиск пути (pathfind) 
			$this->to($follow->position->x, $follow->position->y, $follow->position->z);
		}
		
		if(!$data['max_distance'] || $data['max_distance']<$distance)
		{
			// перезапишем что нам надо идти не к точке (не событие "to" что повесилось выше) а следовать (если повесить вручную другое событие - это будет сброшено тк в 1 группе = 1 событие может быть активно)
			$object->events->add('move/walk', 'follow', ['target'=>$data['target']]);
		}	
	}