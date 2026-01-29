<?php
/**
 * Class SDimmersCctRGB
 *
 * Класс устройств RGB с яркостью для MajorDoMo.
 * Наследуется от SControllers. Описывает свойства яркости, цвета,
 * рабочих параметров, а также методы управления устройством.
 *
 * ===========================================================
 * PROPERTIES:
 * ===========================================================
 *
 * @property string $color           Текущий цвет ленты (HEX 6 символов). 
 *                                   Формат: #RRGGBB или RRGGBB. DataKey. OnChange: propertysUpdated.
 *
 * @property string $colorWork       Рабочий цвет в формате {"x":<value>,"y":<value>}.
 *
 * @property string $colorSaved      Последний установленный цвет (HEX 6 символов).
 *
 * @property int    $level           Текущая яркость (1–100). 
 *                                   DataKey. OnChange: propertysUpdated.
 * 
 * ===========================================================
 * METHODS:
 * ===========================================================
 *
 * @method void setLevel(int $value)
 *      Установить уровень яркости (0–100).  
 *      Вызывается через MajorDoMo: 
 *      `callMethod('Объект.setLevel', array("value" => 0–100))`
 *
 * @method void setColor(string $value)
 *      Установить цвет в HEX формате (#RRGGBB или RRGGBB).  
 *      Вызывается через MajorDoMo: 
 *      `callMethod('Объект.setColor', array("value" => "#RRGGBB"))`
 *
 * @method void levelUp(int $value = 10)
 *      Увеличить яркость на указанное значение.  
 *      Если параметр $value не передан, используется значение по умолчанию 10.  
 *      Вызывается через MajorDoMo: 
 *      `callMethod('Объект.levelUp', array("value" => 1–100))` или просто 
 *      `callMethod('Объект.levelUp')` для +10.
 *
 * @method void levelDown(int $value = 10)
 *      Уменьшить яркость на указанное значение.  
 *      Если параметр $value не передан, используется значение по умолчанию 10.  
 *      Вызывается через MajorDoMo: 
 *      `callMethod('Объект.levelDown', array("value" => 1–100))` или просто 
 *      `callMethod('Объект.levelDown')` для -10.
 *
 * @method void propertysUpdated()
 *      Вызывается при изменении яркости, цвета или сцены.
 *
 * @method void worksUpdated()
 *      Вызывается при изменении рабочих параметров (colorWork / sceneWork).
 */

if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/SDimmersCctRGB_' . SETTINGS_SITE_LANGUAGE . '.php')) {
	include_once(ROOT . 'languages/SDimmersCctRGB_' . SETTINGS_SITE_LANGUAGE . '.php');
} else {
	include_once(ROOT . 'languages/SDimmersCctRGB_default.php'); //
}

$this->device_types['dimmerRGB'] = array(
	'TITLE' => 'Освещение(Диммер RGB)',
	'PARENT_CLASS' => 'SControllers',
	'CLASS' => 'SDimmersCctRGB',
	'DESCRIPTION'=>'Диммер RGB',
	'PROPERTIES' => array(
		'color' => array('DESCRIPTION' => 'Цвет (RGB).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет.', 'ONCHANGE' => 'worksUpdated'),
		'colorSaved' => array('DESCRIPTION' => 'Последний цвет.'),

		'level' => array('DESCRIPTION' => 'Яркость (1<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'levelWork' => array('DESCRIPTION' => 'Рабочая яркость.', 'ONCHANGE' => 'worksUpdated'),
		'levelSaved' => array('DESCRIPTION' => 'Последняя яркость.', 'DATA_KEY' => 1),
		'levelMin' => array('DESCRIPTION' => 'Минимальная рабочая яркость', '_CONFIG_TYPE' => 'num'),
		'levelMax' => array('DESCRIPTION' => 'Максимальная рабочая яркость', '_CONFIG_TYPE' => 'num'),
	),
	'METHODS' => array(
		'levelUp' => array('DESCRIPTION' => 'Увеличить яркость.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'levelDown' => array('DESCRIPTION' => 'Уменьшить яркость.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setLevel' => array('DESCRIPTION' => 'Установить уровень яркости.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setColor' => array('DESCRIPTION' => 'Установиьт цвет(HEX).', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),		
		'worksUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочих параметров'),
		'propertysUpdated' => array('DESCRIPTION' => 'Запускается при смене параметров'),
	),
);
