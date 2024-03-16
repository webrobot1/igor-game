<?php
$new_position = $object->position->next($object->forward);

// если нет прохода дальше - погибает
// todo в будущем сделаем что может лететь и дальше текущей map_id и был лимит длины полета
if(empty(Map2D::getTile($new_position->tile())))
{
	$object->remove();
}
else
{
	$object->action = 'walk';
	$object->position = $new_position;

	$object->events->add('move/walk', 'kamikadze');		
}