<?php
if($object->components->isset('hp') && $object->components->get('hp')>0)
{
	if(!empty($data['target']))
	{
		if(!World::isset($data['target']))
			return;

		$target_object = World::get($data['target']);
		
		if(!$target_object->components->isset('hp') || $target_object->components->get('hp')==0) 
			return;
			
		// при поиске цели может позиции не были равны а потом существо к нам двинулось с другой локации и мы одновременно двинулись  к нему оказаышись в одной точке (нештатная ситуация "гонки процессов")
		if($target_object->position->tile() == $object->position->tile())
			return;					
							
		$object->events->remove('move/walk');				
		$success = false;
		
		$forward = $object->position->forward($target_object->position);
		

		// npc рандомно стреляют магией
		if(!is_a($object, Players::class))
		{
			if(!rand(0,20))
				$data['magic'] = ['firebolt', 'icebolt', 'lightbolt'][rand(0,2)];
		}			
		
		if($data['magic'])
		{
			// првоерить насколько далеко дистанция
			$distance = $object->position->distance($target_object->position);
			
			// в редких случаях $distance = 0 когда из за ошибок в коде мы накладываемся на цель в клетке - и если не првоерять я нулевой forward создам и сервер упадет по ошибке
			if($distance && $distance < ($object->lifeRadius>0?$object->lifeRadius:10))
				$success = $object->position->raycast($target_object->position);		
		}
		else
		{
			// если следующий шаг  - позиция цел
			if($object->position->next($forward)->tile() == $target_object->position->tile())
				$success = true;
		}
		
		//  продолжаем атаку пока недобьем
		$object->events->add('fight/attack', 'index', ['magic'=>$data['magic'], 'target'=>$data['target']]);
			
		if($success)
			$object->forward = $forward;
		else
		{
			$object->events->add('move/walk', 'to', ['x'=>$target_object->position->x, 'y'=>$target_object->position->y, 'z'=>$target_object->position->z]);
			return;					
		}				
	}
	elseif(!empty($data['x']) && !empty($data['y'])) 	// если атакуем по направлению которое дает клиент просто изеним его
	{
		$object->forward = new Forward($data['x'], $data['y']);
	}

	if($data['magic'])
		$object->events->add('fight/bolt', 'index', ['prefab'=>$data['magic']]);		
	else
		$object->events->add('fight/melee');	
}