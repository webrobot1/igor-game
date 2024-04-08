<?php

if($object->components->get('hp') > 0 && $object->components->get('mp') < $object->components->get('mpmax'))
	$object->components->add('mp', min($object->components->get('mpmax'), $object->components->get('mp') + $data['mana']));