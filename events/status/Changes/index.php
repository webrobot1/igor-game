<?php

// что бы не тратить постянно время на расчеты
foreach($data as $key=>$value)
{
	if($key == 'components')
	{
		foreach($value as $name=>$component)
		{
			$object->components->add($name, $value);
		}
	}
	else
		$object->$key = $value;
}
