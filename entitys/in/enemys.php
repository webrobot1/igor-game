<?php
$object = new NewEnemys(...$data);

if(APP_DEBUG)
	$object->log('добавим enemys текущей карты события назначаемые при входе в игру (случайное движение, регенерацию, поиск кого атаковать)');

$object->events->add('status/regenerationhp');
$object->events->add('status/regenerationmp');
$object->events->add('move/randommove');
$object->events->add('fight/search');

return $object;