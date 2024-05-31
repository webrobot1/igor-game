<?php
if(!is_numeric($data['x']) || !is_numeric($data['y']) || ($data['x']==0 && $data['y']==0))
	throw new Exception($object->key.': Невозможно направление x и y по нулям и указанные параметры должны быть реализованы для события');

// если движение инициализировал игрок сбросим атаку и фаерболт который вызвает так же и движение
if($from_client)
{
	$object->events->remove('fight/bolt');
	$object->events->remove('fight/attack');
}

// направление движения
$object->forward = new Forward($data['x'], $data['y']);		

// следующая позиция в указанном направлении длинной в один шаг (системная конанта STEP настраивается в админ панели) окгургденная до числа с количество знаков после запятой как и в STEP
$new_position = $object->position->next($object->forward);

// что бы не хранился поиск пути (то можно отойти , но потом нажать точку туже и он будет старый pathfind использовать)
unset($object->buffer['pathFinding']);

if(empty(Map2D::getTile($new_position->tile())))
{	
	if(APP_DEBUG)
		$object->warning("клетка ".$new_position->tile()." не существует для перехода на нее из события move/walk/index");	
	return;
}

if($object->components->isset('hp') && $object->components->get('hp')>0 
		&& 
	(
		$entitys = World::filter
		(
			$new_position
				,
			static function(EntityAbstract $gameObject) use($object):bool
			{ 
				// если мы живое существо и нам мешает живое существо
				if(
					$gameObject->key!=$object->key
						&&
					$gameObject->components->isset('hp')
						&& 
					$gameObject->components->get('hp')>0
						&&
					(!($gameObject instanceOf Players) || !($object instanceOf Players))
				) 
					return true; 
				else
					return false;
			}
		)
	)
)
{
	if(APP_DEBUG)
		$object->warning("клетка ".(string)$new_position." занята для прохода (".implode(', ', array_keys($entitys)).") из события move/walk/index");
	
	return;	
}

$object->action = 'walk';
$object->position = $new_position;		