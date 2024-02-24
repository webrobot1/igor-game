<?php
if(!$object->components->isset('hp') || $object->components->get('hp')==0)
{
	$object->action = "dead";
	$object->events->remove('move/walk');
}