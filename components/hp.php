<?php

// если у нас сработал тигер и стало максимум жизней (например прибвалось здоровье или мы вошли в игру) ничего не делаем
if($object->components->get('hp')<$object->components->get('hpmax'))
{
	// если было до момента изменения жизней смены жизней максимально здоровья сбросим таймаут регенерации (запустим отсчет ее по новой с этого момента) - а том можем сразу вылечиться после удара
	if ($old_value  ==  $object->components->get('hpmax'))
		$object->events->get("status/regeneration")->resetTimeout();

	if ($object->components->get('hp') == 0)
	{
		$object->events->add("status/resurrect");
		
		//Если так добавляемся в мир (например при переходе между локациями или вход в игру) - НЕ меняем анимацию и НЕ сдвигаем таймаут
		//признак того что код создается во время добавления сущетва в игру  - старое значение равно текущему
		if($old_value != $object->components->get('hp'))
		{
			$object->events->add("status/dead");
			$object->events->get("status/resurrect")->resetTimeout();
		}
	}
}