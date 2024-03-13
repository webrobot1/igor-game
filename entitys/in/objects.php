<?php

$object = new NewObjects(...$data);

if($object->prefab=="Altar")
	$object->events->add('object/portal');

return $object;
