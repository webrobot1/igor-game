<?php
if(APP_DEBUG)
	$object->log('добавим enemys текущей карты события назначаемые при входе в игру (случайное движение, регенерацию, поиск кого атаковать)');

$object->events->add('status/regeneration');
$object->events->add('move/randommove');
$object->events->add('fight/search');