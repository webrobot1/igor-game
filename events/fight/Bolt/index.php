<?php
if(!$object->components->isset('hp') || $object->components->get('hp')>0)
{
	$spellbook = Components::list()['spellbook']['default'];
	$object_spells = $object->components->get('spellbook');
	
	if(empty($object_spells[$data['prefab']]))
		throw new Exception('не найдено заклинание '.$data['prefab']);
	
	$cost = $spellbook[$data['prefab']]['mp'];
	$current = $object->components->get('mp');
	
	if($current>=$cost)
	{
		$object->action = 'attack';
		$object->components->add('mp', $current - $cost);
		
		// полный код механики доступен в фаиле php данного события
		$new_object = [ 
			'prefab' => $data['prefab'], 
			'lifeRadius' => $object->lifeRadius?:1, 
			'map_id' => MAP_ID, 
			'action' => 'walk', 
			'forward_x' => $object->forward->x, 
			'forward_y' => $object->forward->y, 
			'forward_z' => $object->forward->z, 
			'sort' => $object->sort, 
			'x' => $object->position->x,               
			'y' => $object->position->y,
			'z' => $object->position->z,
			'components'=>array
			(
				'speed'=>5,
				'owner'=>$object->key,
			)
		];	

		// добавим сущность на карту (всем разошлется автоматом о появлении нового объекта)
		$new_object = World::add(EntityTypeEnum::Objects, $new_object);
		
		// добавим в механику обработки столкновений
		$new_object->events->add('status/collision', 'bolt');
		
		// + добавим механику полета до первого препятсвия карты
		$new_object->events->add('move/walk', 'kamikadze', ['max_distance'=>$data['max_distance']]);

		// добавим паузу к движению заклинателя (стагернем)
		$object->events->add('status/stagger');
	}
}