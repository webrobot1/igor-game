<?php

World::filter($object->position, static function(EntityAbstract $object):bool
{
	if(is_a($object, Players::class) && $object->components->isset("hp") && $object->components->get("hp")==0) 
	{
		$object->components->add("hp", 0);
		$object->action = "resurrect";
	}
	return false;
}, $data['distance']);