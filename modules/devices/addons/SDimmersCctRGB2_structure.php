<?php
/**
 * Class SDimmersCctRGB2
 *
 * Класс устройств RGB с яркостью для MajorDoMo.
 * Наследуется от SControllers. Описывает свойства яркости, цвета,
 * рабочих параметров, а также методы управления устройством.
 *
 * ===========================================================
 * PROPERTIES:
 * ===========================================================
 *
 * @property int    $level           Яркость (1–100). OnChange: propertysUpdated. DataKey.
 * @property int    $levelWork       Рабочая яркость. OnChange: worksUpdated. DataKey.
 * @property int    $levelSaved      Сохраненная яркость.
 * @property int    $levelMaxWork    Максимальная рабочая яркость (config).
 * @property int    $levelMinWork    Минимальная рабочая яркость (config).
 * @property int    $cct             Температура (1–100). OnChange: propertysUpdated. DataKey.
 * @property int    $cctWork         Рабочая теплота.  OnChange: worksUpdated. DataKey.
 * @property int    $cctSaved        Сохраненная теплота.
 * @property int    $cctMaxWork      Максимальная рабочая теплота (config).
 * @property int    $cctMinWork      Минимальная рабочая теплота (config).
 * @property string $color           Текущий цвет ленты (HEX 6 символов).Формат: #RRGGBB или RRGGBB. DataKey. OnChange: propertysUpdated.
 * @property string $colorWork       Рабочий цвет в формате {"x":<value>,"y":<value>}. OnChange: worksUpdated. DataKey.
 * @property string $colorSaved      Последний установленный цвет (HEX 6 символов).
 * 
 * @property int    $mode            Что включать (цвет, температура) (config).
 * @property int    $dayMode         Что включать днем (цвет, температура) (config).
 * @property int    $nightMode       Что включать ночью (цвет, температура) (config).
 * @property int    $autoOnOff       Автовключение (config).
 * @property int    $timerOff        Выключить через (сек). 0 - не выключать (config).
 * @property int    $workingDay      Включать (день, ночь, 24 часа) (config).
 * @property int    $workingBy       Работать по (время, солнце, датчик) (config).
 * @property string $dayBegin        Начало режима день (hh:mm) (config).	
 * @property string $nightBegin      Начало режима ночь (hh:mm) (config).
 * @property string $sunriseTime     Время восхода солнца.
 * @property string $sunsetTime      Время захода солнца.
 * @property int    $signSunrise     Восход (прибавить/отнять) (config).
 * @property string $addTimeSunrise  Часов:Минут (00:00) (config).
 * @property int    $signSunset      Закат (прибавить/отнять) (config).
 * @property string $addTimeSunset   Часов:Минут (00:00) (config).
 * @property int    $illuminanceMax  Макc.освещение (датчик) (config).
 * @property int    $illuminanceFlag Стопер датчика освещения (config).
 * @property int    $illuminance     Данные с датчика освещения. DataKey.
 * @property int    $presence        Данные с датчика присутствия. OnChange: propertysUpdated. DataKey.
 * @property int    $flag            Стопер запуска авто мода (config).	
 * 
 * ===========================================================
 * METHODS:
 * ===========================================================
 *
 * @method void turnOn()                Включить устройство
 * @method void turnOff()               Выключить устройство
 * @method void switch()                Переключить состояние (вкл/выкл)
 * @method void setLevel(int $value)    Установить уровень яркости (1–100)
 * @method void setCct(int $value)      Установить уровень температуры (1–100)
 * @method void levelUp(int $value)     Увеличить уровень яркости
 * @method void levelDown(int $value)   Уменьшить уровень яркости
 * @method void cctUp(int $value)       Увеличить уровень температуры
 * @method void cctDown(int $value)     Уменьшить уровень температуры
 * @method void setColor(string $value) Установить цвет в HEX формате (#RRGGBB или RRGGBB).  
 * @method void propertysUpdated()      Запускается при смене яркости, теплоты, присутствия
 * @method void worksUpdated()          Запускается при смене рабочей яркости и цвета
 * @method void byDefault()             Установить свойства по умолчанию
 * @method void createCommandsMenu()    Создает меню управления
 * @method void deleteCommandsMenu()    Удаляет меню управления
 */

if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/SDimmersCctRGB2_' . SETTINGS_SITE_LANGUAGE . '.php')) {
	include_once(ROOT . 'languages/SDimmersCctRGB2_' . SETTINGS_SITE_LANGUAGE . '.php');
} else {
	include_once(ROOT . 'languages/SDimmersCctRGB2_default.php'); //
}

$this->device_types['dimmerCctRGB'] = array(
	'TITLE' => 'Освещение(Диммер CCT RGB)',
	'PARENT_CLASS' => 'SControllers',
	'CLASS' => 'SDimmersCctRGB2',
	'DESCRIPTION'=>'Освещение(Диммер CCT RGB)',
	'PROPERTIES' => array(
		'color' => array('DESCRIPTION' => 'Цвет (RGB).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'colorWork' => array('DESCRIPTION' => 'Рабочий цвет.', 'ONCHANGE' => 'worksUpdated'),
		'colorSaved' => array('DESCRIPTION' => 'Последний цвет.'),

		'level' => array('DESCRIPTION' => 'Яркость (1<-->100).', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'levelWork' => array('DESCRIPTION' => 'Рабочая яркость.', 'ONCHANGE' => 'worksUpdated'),
		'levelSaved' => array('DESCRIPTION' => 'Последняя яркость.', 'DATA_KEY' => 1),
		'levelMax' => array('DESCRIPTION' => 'Максимальная рабочая яркость', '_CONFIG_TYPE' => 'num'),
		'levelMin' => array('DESCRIPTION' => 'Минимальная рабочая яркость', '_CONFIG_TYPE' => 'num'),

		'cct' => array('DESCRIPTION' => 'Температура (1-100)', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'cctWork' => array('DESCRIPTION' => 'Рабочая теплота.', 'ONCHANGE' => 'worksUpdated'),
		'cctSaved' => array('DESCRIPTION' => 'Сохраненная теплота.'),
		'cctMax' => array('DESCRIPTION' => 'Максимальная рабочая теплота', '_CONFIG_TYPE' => 'num'),
		'cctMin' => array('DESCRIPTION' => 'Минимальная рабочая теплота', '_CONFIG_TYPE' => 'num'),

		'dayColor' => array('DESCRIPTION' => 'Цвет днем', '_CONFIG_TYPE' => 'num',),
		'dayLevel' => array('DESCRIPTION' => 'Уровень яркости днем', '_CONFIG_TYPE' => 'num',),
		'dayCct' => array('DESCRIPTION' => 'Уровень теплоты днем', '_CONFIG_TYPE' => 'num',),
		'dayMode' => array('DESCRIPTION' => 'Что включать днем (цвет, температура)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Температура'),

		'nightColor' => array('DESCRIPTION' => 'Цвет ночью', '_CONFIG_TYPE' => 'num',),
		'nightLevel' => array('DESCRIPTION' => 'Уровень яркости ночью', '_CONFIG_TYPE' => 'num',),
		'nightCct' => array('DESCRIPTION' => 'Уровень теплоты ночью', '_CONFIG_TYPE' => 'num',),
		'nightMode' => array('DESCRIPTION' => 'Что включать ночью (цвет, температура)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Температура'),

		'mode' => array('DESCRIPTION' => 'Что включать (цвет, температура)','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Цвет,2=Температура'),

		'autoOnOff' => array('DESCRIPTION' => 'Автовключение','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Включено,0=Отключено'),
		'timerOff' => array('DESCRIPTION' => 'Выключить через(сек). 0-не выключать', '_CONFIG_TYPE' => 'num'),
		'workingDay' => array('DESCRIPTION' => 'Включать','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=День,2=Ночь,3=24 часа'),
		'workingBy' => array('DESCRIPTION' => 'Работать по','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=Время,2=Солнце,3=Датчик'),
		'dayBegin' => array('DESCRIPTION' => 'Начало режима день(hh:mm)', '_CONFIG_TYPE' => 'num'),
		'nightBegin' => array('DESCRIPTION' => 'Начало режима ночь(hh:mm)', '_CONFIG_TYPE' => 'num'),
		'sunriseTime' => array('DESCRIPTION' => 'Время восхода солнца'),
		'sunsetTime' => array('DESCRIPTION' => 'Время захода солнца'),
		'signSunrise' => array('DESCRIPTION' => 'Восход','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=прибавить,0=отнять'),
		'addTimeSunrise' => array('DESCRIPTION' => 'Часов:Минут(00:00)', '_CONFIG_TYPE' => 'num'),
		'signSunset' => array('DESCRIPTION' => 'Закат','_CONFIG_TYPE'=>'select','_CONFIG_OPTIONS'=>'1=прибавить,0=отнять'),
		'addTimeSunset' => array('DESCRIPTION' => 'Часов:Минут(00:00)', '_CONFIG_TYPE' => 'num'),
		'illuminanceMax' => array('DESCRIPTION' => 'Макc.освещение(датчик)', '_CONFIG_TYPE' => 'num'),
		'illuminanceFlag' => array('DESCRIPTION' => 'Стопер датчика освещения'),
		'illuminance' => array('DESCRIPTION' => 'Данные с датчика освещения', 'DATA_KEY' => 1),
		'presence' => array('DESCRIPTION' => 'Данные с датчика присутствия', 'ONCHANGE' => 'propertysUpdated', 'DATA_KEY' => 1),
		'flag' => array('DESCRIPTION' => 'Стопер запуска авто мода'),
	),
	'METHODS' => array(
		'turnOn' => array('DESCRIPTION' => 'Включить', '_CONFIG_SHOW' => 1),
		'turnOff' => array('DESCRIPTION' => 'Выключить', '_CONFIG_SHOW' => 1),
		'switch' => array('DESCRIPTION' => 'Переключить'),
		'levelUp' => array('DESCRIPTION' => 'Увеличить яркость.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'levelDown' => array('DESCRIPTION' => 'Уменьшить яркость.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setLevel' => array('DESCRIPTION' => 'Установить уровень яркости.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctUp' => array('DESCRIPTION' => 'Увеличить уровень температуры.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'cctDown' => array('DESCRIPTION' => 'Уменьшить уровень температуры.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setCct' => array('DESCRIPTION' => 'Установить уровень температуры.', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),
		'setColor' => array('DESCRIPTION' => 'Установить цвет(HEX).', '_CONFIG_SHOW' => 1, '_CONFIG_REQ_VALUE' => 1),		
		'worksUpdated' => array('DESCRIPTION' => 'Запускается при смене рабочих параметров'),
		'propertysUpdated' => array('DESCRIPTION' => 'Запускается при смене параметров'),
		'createCommandsMenu' => array('DESCRIPTION' => 'Создает меню управления.', '_CONFIG_SHOW' => 1),
		'deleteCommandsMenu' => array('DESCRIPTION' => 'Удаляет меню управления.', '_CONFIG_SHOW' => 1),	
		'byDefault' => array('DESCRIPTION' => 'Метод по умолчанию'),
	),
);
