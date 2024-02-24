<?php

// по диагонали таймаут больше
return 1 / $object->components->get('speed') * (abs($object->forward->x) + abs($object->forward->y));
