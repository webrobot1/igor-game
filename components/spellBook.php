<?php
# Этот код и при создании существ проверит значение компонента и далее из события что будет меняться
$component = Components::list()['spellbook'];

// на случай если кто то захочет изменить в админке (это не то что плохо - просто не надо)
if($component['max_compare_level'])
	throw new Error('Компонент settins не должен иметь в настройках предела уровня анализа уникальных данных для Рассылки');
	
$current_value = $object->components->get('spellbook');	
	
$spells = array();	
foreach($component['default'] as $key=>&$spell)
{
	if(!$spell['available'] && empty($value[$key]))
	{
		unset($component['default'][$key]);
		continue;
	}
	
	// в клиенсткой части поле не нужно
	unset($spell['available']);
	
	// игроку в комеоненты в итоге сохраним ключ->значение а если только на сцену добавляется вышлем пакет настроек без value (значение) напрямую(методом send с сигнатурой как пакеты компонентов выглядят)  тк они придут следлм
	$spells[$key] = true;
}


// при создании существа на сцене вышлем его настройки с указанием типов и доп данных или если какое то заклинание появилось или удалилось
if($object->type == EntityTypeEnum::Players && (!World::isset($object->key) || $current_value!=$spells[$key]))
{
	$object->send(['spellbook'=>$component['default']]);	
}


// тот самый случай когда нам надо переопределить value (считайте что он по ссылке передан) которая в итоге присвоиться (мы не передаем с киента тип настройк например , а только значение, но значение новое должно содержать все)
$value = $spells;