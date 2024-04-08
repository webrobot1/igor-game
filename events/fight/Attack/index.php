<?php
if($object->components->isset('hp') && $object->components->get('hp')>0)
{
	if(!empty($data['target']))
	{
		// уберем механику преследования и движения перед тем как првоерить возможность атаковать
		$object->events->remove('move/walk');
		
		if(!World::isset($data['target']))
			return;

		$target_object = World::get($data['target']);
		
		if(!$target_object->components->isset('hp') || $target_object->components->get('hp')==0) 
			return;
			
		// при поиске цели может позиции не были равны а потом существо к нам двинулось с другой локации и мы одновременно двинулись  к нему оказаышись в одной точке (нештатная ситуация "гонки процессов")
		if($target_object->position->tile() == $object->position->tile())
			return;					
		
		$distance = $object->position->distance($target_object->position);
		
		// если атакующих не видит уже цель то перестанем атаковать и защищающийся не видит атакующего
		if($distance>$object->lifeRadius && $distance>$target_object->lifeRadius)
			return;
					
		$success = false;
		$forward = $object->position->forward($target_object->position);
		
		// если следующий шаг  - позиция цел
		if($object->position->next($forward)->tile() == $target_object->position->tile())
			$success = true;

		// npc стреляют рандомной магией если игрок не рядом и есть заклинания в их книге и маны достаточно
		if(!$success && !($object instanceOf Players) && $object->components->isset('spellbook') && $object->components->isset('mp'))
		{
			$spellbook = Components::list()['spellbook']['default'];
			$object_spells = $object->components->get('spellbook');
			$currentMp = $object->components->get('mp');
			
			$bolts = ['firebolt', 'icebolt', 'lightbolt'];
			shuffle($bolts);
			foreach($bolts as $spell)
			{
				if(isset($object_spells[$spell]) && $currentMp>=$spellbook[$spell]['mp'])
				{
					$data['magic'] = $spell;
					break;
				}
			}
		}			
		
		if(!empty($data['magic']))
		{
			// првоерить насколько далеко дистанция
			// в редких случаях $distance = 0 когда из за ошибок в коде мы накладываемся на цель в клетке - и если не првоерять я нулевой forward создам и сервер упадет по ошибке
			if($distance && $distance < ($object->lifeRadius>0?$object->lifeRadius:10))
				$success = $object->position->raycast($target_object->position);	

			if(!$success && ($object instanceOf Players))
				$data['magic'] = '';
		}

		// продолжаем атаку пока недобьем
		// игроки которые выстрелили магией (кроме случаев когда надо подойти к цели) атаку не продолжают (пусть кликают на кнопку)
		if(empty($data['magic']) || !($object instanceOf Players) || !$success)
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