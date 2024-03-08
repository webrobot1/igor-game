<?php
# Этот код и при создании существ проверит значение компонента и далее из события что будет меняться
$default = Components::list()['settings']['default'];

// если существо создается на сцене будут присвоено значение по умолчанию
// и если ранее были настройки которых в значениях по умолчанию сейчас нет - они будут удалены (не будут обработаны ниже)
// таким образом мы можем менять настройки частично в том числе
$settings = array();
foreach($default as $key=>&$setting)
{
	// из события мы отправляем просто значения , а при первом присвоении значения при создании существа на сцене это много мерный массив
	if(array_key_exists($key, $value))
	{
		$current_value = $value[$key]['value']??$value[$key];
		switch($setting['type'])
		{
			case 'checkbox':
				if(!is_numeric($current_value))
					throw new Error('значение настроек чекбокса должно быть числом');
				
				$current_value = (int)$current_value;
				
				if(!in_array($current_value, [0, 1]))
					throw new Error('недопустимое значение '.$current_value.' выпадающего списка '.$key);
			break;		
			
			case 'dropdown':
				if(empty($setting['values']))
					throw new Error('У копонента настроек dropdown не указан список допустимых значений');
				
				if(!isset($setting['values'][$current_value]))
					throw new Error('недопустимое значение '.$current_value.' выпадающего списка '.$key);
			break;		
			
			case 'slider':
				if(!is_numeric($current_value))
					throw new Error('значение настроек слайдера ('.$current_value.') должно быть числом');
				
				$current_value = (int)$current_value;
				
				if(isset($setting['max']) && $current_value>$setting['max'])
					throw new Error('значение настроек слайдера должно быть не больше '.$setting['max']);			
				
				if(isset($setting['min']) && $current_value<$setting['min'])
					throw new Error('значение настроек слайдера должно быть не меньше '.$setting['min']);
				
				if($key == 'radius')
					$object->lifeRadius = $current_value;
				
			break;
			
			default:
				throw new Error('Неизвестный тип настроек '.$setting['type']);
			break;
		}
					
		$setting['value'] = $current_value;
	}
	
	// игроку в комеоненты в итоге сохраним ключ->значение а если только на сцену добавляется вышлем пакет настроек без value (значение) напрямую(методом send с сигнатурой как пакеты компонентов выглядят)  тк они придут следлм
	$settings[$key] = $setting['value'];
	unset($setting['value']);
}


// при создании существа на сцене вышлем его настройки с указанием типов и доп данных
if(!World::isset($object->key))
{
	$object->send(['settings'=>$default]);	
}


// тот самый случай когда нам надо переопределить value (считайте что он по ссылке передан) которая в итоге присвоиться (мы не передаем с киента тип настройк например , а только значение, но значение новое должно содержать все)
$value = $settings;