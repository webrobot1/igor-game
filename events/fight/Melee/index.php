<?php
if($object->components->isset('hp') && $object->components->get('hp')>0)
{
	$object->action = 'attack';	
	$object->events->add('status/stagger');	// добавим паузу к движению атакующего (стагернем)
		
	// на какую позицию существо смотрит
	$target_position = $object->position->next($object->forward);
	$type = $object->type;	
	
	// проверим есть ли в стороне в которой мы атакуем кто то
	if(
		$gameObjects = World::filter
		(
			$target_position
				,
			function(EntityAbstract $gameObject) use($type):bool
			{ 
				if(
					$gameObject->components->isset('hp') 
						&& 
					$gameObject->components->get('hp')>0 
		//				&& 						
		//			$type != $gameObject->type						
				) 
					return true;
				else
					return false;
			}
				,
			count: null // вообще на клетке может быть лишь одно существо, но на всяки случай выберем всех на позиции
		)		
	)
	{
		foreach($gameObjects as $gameObject)
		{
			$gameObject->events->add('status/hurt', 'index', ['hp'=>1, 'from'=>$object->key]);	// все что нужно сделать помимо отнимания жизней хранится внутри события (стегерим. развернем и тп)	
		}	
	}
}