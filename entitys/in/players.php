<?php

$object = new NewPlayers(...$data);

// хоть мы и не выполянем события ссуществ с других карт засорять не будем список событий
if(APP_DEBUG)
	$object->log('добавим игроку текущей карты события назначаемые при входе в игру (авто сохранение и дисконнект по таймауту)');

$object->events->add('status/regenerationhp');
$object->events->add('status/regenerationmp');

$object->events->add(SystemActionEnum::EVENT_SAVE, from_client: true);
$object->events->get(SystemActionEnum::EVENT_SAVE)->resetTimeout();				// сбросим обработку события через его таймаут (а то на текущем кадре выполнится и отсоединит игрока)

$object->events->add(SystemActionEnum::EVENT_DISCONNECT,  from_client: true);
$object->events->get(SystemActionEnum::EVENT_DISCONNECT)->resetTimeout();

return $object;
