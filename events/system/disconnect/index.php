<?php

$timeout = $object->events->get('system/disconnect')->timeout;
if($object->last_active+$timeout<microtime(true))
{
	$object->send(['error'=>'превышен лимит ожидания '.$timeout.' сек.']);
	$object->remove($object->key);
}