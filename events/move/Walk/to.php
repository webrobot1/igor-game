<?php
if(!is_numeric($data['x']) || !is_numeric($data['y']))
	throw new Exception($object->key.': Невозможно идти к точкам x и y если это не цифровые координаты');

	// если движение инициализировал игрок сбросим атаку и фаерболт который вызвает так же и движение
	if($from_client)
	{
		$object->events->remove('fight/bolt');
		$object->events->remove('fight/attack');
	}
	
	$vector = (new Position($data['x'], $data['y']));
	
	// если осталось меньше шага то путь уже не проложим
	if($object->position->tile() == $vector->tile())
	{
		return;	
	}
	
	$tile = $vector->tile();
	if(empty(Map2D::getTile($tile)))
	{
		if(APP_DEBUG)
			$object->warning("клетка ".$tile.' не проходима для движения из события move/walk/to');
		
		return;
	}	
			
	if(empty($object->buffer['pathFinding']) || end($object->buffer['pathFinding']) != $tile)
	{
		// нельзя пройти
		if(!$object->buffer['pathFinding'] = PathFinding::astar($object->position->tile(), $tile))
		{
			if(APP_DEBUG)
				$object->warning("нельзя пройти из ".(string)$object->position." в ".$tile.' из события move/walk/to');
			
			return;
		}	
	}
	
	$new_position = new Position(...explode(Position::DELIMETR, $object->buffer['pathFinding'][0]));
	
	// при получении первого существа на локации означает что локация не проходима
	// если только мы не призрак или вообще нет hp (тогда можем на локацию существа наложиться)
	if($object->components->isset('hp') && $object->components->get('hp')>0 
			&& 
		World::filter
		(
			$new_position
				,
			function(EntityAbstract $gameObject):bool
			{ 
				// если мы живое существо и нам мешает живое существо
				if(
					$gameObject->components->isset('hp')
						&& 
					$gameObject->components->get('hp')>0					
				) 
					return true; 
				else
					return false;
			}
		)
	)
	{
		if(APP_DEBUG)
			$object->warning("клетка ".(string)$new_position." занята для прохода из события move/walk/to");	
		
		$object->buffer['pathFinding'] = null;
		return;			
	}
	
	
	// здесь есть ошибка - мы одталкиваемся от фактического расположения существа и если двигаемся к направлению точки которая найдена через поиск пути мы можем следующем шагом пойти по диагонали и зайти на несуществующуб клетку по оси x или y
	$object->forward = $object->position->forward($new_position);	
	$position = $object->position->next($object->forward);
	
	if(empty(Map2D::getTile($position->tile())))
	{
		// возможно как то можно чуть чуть отклонить forward для того что бы он попадал в существующую клетку если заходит на непроходимую, но тут нужна формула учитывающая куда отклонять 
		$position = $new_position;
		
		//todo увеличить таймаут за пройденные лишние клетки за раз
		$object->events->get('move/walk')->resetTime($object->events->get('move/walk')->timeout*2);	
	}
	
	$object->position = $position;
	
	// по диагонали мы ходим в два шага поэтому если уже перешли на второй шаг (это уже клетка назначения будет) - то удаляем ...ну или если не по диаогонали ходим тоде удаляем тк там один шаг
	// извлекаем позицию из списка с пересортировкой ключей
	if($object->position->tile() == $new_position->tile())
		array_shift($object->buffer['pathFinding']);
		
	$object->action = 'walk';
	$object->events->add('move/walk', 'to', ['x'=>$data['x'], 'y'=>$data['y'], 'z'=>$data['z']]);