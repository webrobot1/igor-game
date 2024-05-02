<?php
# Этот код и при создании существ проверит значение компонента и далее из события что будет меняться
$component = Components::list()['actionbars'];

// на случай если кто то захочет изменить в админке (это не то что плохо - просто не надо)
if($component['max_compare_level']!=2)
	throw new Error('Компонент settins не должен иметь в настройках предела уровня анализа уникальных данных для Рассылки кроме как 1');
	
$current_value = $object->components->get('actionbars');

$spells = $object->components->get('spellbook');
$inventory = $object->components->get('inventory');


// эти танц с бубнами от 1 нужны для c# что бы передавать не как массив данные а как объект (тк если от 0 будет отсчет c# воспримит как массив , несмотря на то что там принудительно стоит Dictionary)
$actionbars = array();
for($i=1;$i<=count($component['default']);$i++)
{	
	if(!empty($value[$i]))
	{	
		switch($value[$i]['type'])
		{
			case 'spell':
				if(empty($spells[$value[$i]['id']]))
				{
					$value[$i] = ['type'=>'', 'id'=>''];
				}
			break;
			case 'item':
				if(empty($inventory[$value[$i]['id']]))
				{
					$value[$i] = ['type'=>'', 'id'=>''];
				}
			break;		
			default:
				$value[$i]['type'] = '';
				$value[$i]['id'] = '';		
			break;
		}
		$current_value[$i] = $value[$i];
	}
	// можем слать null значение на кнопку поэтмоу оно может быть array_key_exists, но при этом empty
	elseif(array_key_exists($i, $value) || empty($current_value[$i]['type']) || empty($current_value[$i]['id']))
	{
		$current_value[$i] = ['type'=>'', 'id'=>''];
	}
	
	$actionbars[$i] = $current_value[$i];
}

$value = $actionbars;