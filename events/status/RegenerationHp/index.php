<?php

if($object->components->get('hp') > 0 && $object->components->get('hp') < $object->components->get('hpmax'))
	$object->components->add('hp', min($object->components->get('hpmax'), $object->components->get('hp') + $data['life']));