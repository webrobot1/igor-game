<?php

World::filter($object->position, static function(EntityAbstract $object) use($data):bool
{
	if(is_a($object, Players::class))
	{
		$object->events->add("status/changes", "index", ["data"=>var_export($data['data'], true)]);		
	}

	return false;
}, $data['distance'], $data['count']);