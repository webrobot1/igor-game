<?php

// если уходим с картой и есть какая то точка к которой идем возможно мы как раз идем на другую карту и надо пересчитать координаты
if($new_map)
{
	// если в момент выхода из игры мы куда то шли и мы переходим на нову карту в этот момент и эта карта соседняя - пересчитаем относительно нее координат
	if($object->events->get('move/walk')->action=='to' && !empty(Map2D::sides()[$new_map]))
	{
		$data = $object->events->get('move/walk')->data;
		Map2D::encode2dCoord($data['x'], $data['y'], MAP_ID, $new_map);
		
		// перезпишет
		$object->events->add('move/walk', 'to', $data);
	}
}