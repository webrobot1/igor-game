<?php
if($data['hp']>0)
{
	$current_hp = $object->components->get('hp');
	if($data['hp'] > $current_hp) 
		$data['hp'] = $current_hp;
	
	$object->action = 'hurt';																// укажем событие для анимации
	
	// остановим (застагерим) при попадании	объект в который попали. 
	//тк они могу выполнятся в следующем кадре одновременно и тем самым игрока анимация не будет доступна на клиенте
	$object->events->add('status/stagger');												
	
	$total = $current_hp - $data['hp'];
	$object->components->add('hp', $total);													// отнимим жизни	
	
	if($total && $data['from'] && World::isset($data['from']))
	{
		$gameObject = World::get($data['from']);
		if(!is_a($gameObject, Objects::class) && $object::class != $gameObject::class)	    // если нас ранил не объект и это другого типа существо атакуем его
		{	
			if(empty($object->events->get('fight/attack')->action)) 						// если не атакуем никого то начнем атаковать того кто в нас ранит рукопашкой	
				$object->events->add('fight/attack', 'index', ['target'=>$data['from']]);			
			elseif(!empty($object->events->get('fight/attack')->data['magic']))	            // или атакуем но магией
			{
				$object->forward = $object->position->forward($gameObject->position);		// развернем в сторону с которой был запущен фаерболт
				$object->events->add('fight/melee');										// и пнем рукопашкой
			}					
		}
	}		
}