<?php
/** Краткое описание всех функций
 *
 * normalizeRange($val, $min, $max, $type) — Проверяет и нормализует значение (число или HEX).
 * xyToHex($x, $y) — Конвертирует CIE XY координаты в HEX цвет.
 * hexToXy($hex) — Конвертирует HEX цвет в массив CIE XY координат.
 * levelToWork($level, $min, $max, $limitMin) — Масштабирует проценты (0-100) в рабочий диапазон устройства.
 * workToLevel($val, $min, $max, $limitMin) — Масштабирует рабочее значение устройства в проценты (0-100).
 * dimmerTime($time, $addTime, $sign) — Вычисляет новое время с поправкой (добавить/вычесть HH:MM).
 * autoOff($object, $timer, $flag, $presence) — Запускает таймер автоотключения лампы.
 * initDefaults($object, $defaults) — Инициализирует свойства объекта по умолчанию.
 * getAutoLevelCct($object, $level, $cct, $color, $colorLevel, $sceneName)
 *   — Получает текущие значения яркости, CCT, цвета, уровня цвета и сцены для авто режима.
 * adjustProperty($obj, $property, $value, $direction, $defaultStep, $min, $max)
 *   — Универсальное изменение свойства (увеличить/уменьшить).
 * createCommandsMenu($objectName, $menuItems, $parentId, $insertID, $depth)
 *   — Создает меню управления объектом рекурсивно.
 * deleteCommandsMenu($objectName, $menuItems)
 *   — Удаляет команды меню по структуре $menuItems,
 *   — используя TITLE и LINKED_OBJECT, включая вложенные
 *   — команды по SUB_LIST (рекурсивно).

 *--------------------------------------------------------------------------------------------
 *| Функция            | Назначение                                                           |
 *| ------------------ | ---------------------------------------------------------------------|
 *| `normalizeRange`   | Нормализует HEX или число в диапазон                                 |
 *| `adjustProperty`   | Универсальное изменение свойства лампы                               |
 *| `xyToHex`          | CIE XY координаты -> HEX цвет (#RRGGBB)                              |
 *| `hexToXy`          | HEX цвет -> массив ['x' => ..., 'y' => ...]                          |
 *| `levelToWork`      | Проценты (0-100) -> Рабочий диапазон (min..max)                      |
 *| `workToLevel`      | Рабочий диапазон (min..max) -> Проценты (0-100)                      |
 *| `dimmerTime`        | Корректирует время с учётом смещения                                |
 *| `autoOff`           | Таймер автоотключения лампы                                         |
 *| `initDefaults`      | Устанавливает свойства объекта по умолчанию                         |
 *| `getAutoLevelCct`   | Возвращает яркость, CCT, цвет, уровень цвета и сцену авто режима    |
 *| `createCommandsMenu'| Рекурсивное создание меню управления объектом                       |
 *| `deleteCommandsMenu`| Удаление меню управления объектом                                   |
*-----------------------------------------------------------------------------------------------
 */
//


/** Проверяет и нормализует значение: числовое или HEX (цвет/яркость).
* @param mixed  $val  Входное значение (число или HEX)
* @param int    $min  Минимальное значение диапазона для чисел
* @param int    $max  Максимальное значение диапазона для чисел
* @param string $type Тип значения: 'auto', 'number', 'color'
* @return int|string|null
*/
if (!function_exists('normalizeRange')) {
    function normalizeRange($val, $min = 0, $max = 100, $type = 'auto') {
        $val = strtolower(trim($val));
        if ($type === 'number') {
            if (is_numeric($val)) {
                return (int)max($min, min($max, $val));
            }
            return null;
        }
        if ($type === 'color' || $type === 'auto') {
            if (preg_match('/^[0-9a-f]{12}$/i', $val)) return $val;
            $hex = ltrim($val, '#');
            if (preg_match('/^[0-9a-f]{6}$/i', $hex)) return '#' . $hex;
            if (preg_match('/^[0-9a-f]{3}$/i', $hex)) {
                return '#' . $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
        }
        return null;
    }
}

/** Универсальное изменение свойств (яркость, температура и т.п.)
* @param object $obj — объект ($this)
* @param string $property — имя свойства ('level', 'cct')
* @param mixed $value — шаг изменения
* @param string $direction — 'up' или 'down'
*/
if (!function_exists('adjustProperty')) {
    function adjustProperty($obj, $property, $value = null, $direction = 'up', $defaultStep = 10, $min = 0, $max = 100) {
        $current = (int)$obj->getProperty($property);
        $step = is_numeric($value) ? (int)$value : $defaultStep;
        $step = max(1, min($max, abs($step)));
        $newValue = ($direction === 'up') ? min($max, $current + $step) : max($min, $current - $step);
        $obj->callMethod("set" . ucfirst($property), ['value' => $newValue]);
    }
}

/** Конвертирует CIE XY координаты в HEX цвет (#RRGGBB).
* @param float $x Координата X (0.0 - 1.0)
* @param float $y Координата Y (0.0 - 1.0)
* @return string HEX цвет
*/
if (!function_exists('xyToHex')) {
    function xyToHex($x, $y) {
        $x = (float)$x; $y = (float)$y;
        $Y_ref = 1.0; 
        $safeY = ($y < 0.000001) ? 0.000001 : $y;
        $X = ($Y_ref / $safeY) * $x;
        $Z = ($Y_ref / $safeY) * (1.0 - $x - $y);
        $r = $X * 3.2406 - $Y_ref * 1.5372 - $Z * 0.4986;
        $g = -$X * 0.9689 + $Y_ref * 1.8758 + $Z * 0.0415;
        $b = $X * 0.0557 - $Y_ref * 0.2040 + $Z * 1.0570;
        $r = max(0, $r); $g = max(0, $g); $b = max(0, $b);
        $maxChannel = max($r, $g, $b);
        if ($maxChannel > 0) { $r /= $maxChannel; $g /= $maxChannel; $b /= $maxChannel; }
        $hex = '#';
        foreach ([$r, $g, $b] as $v) {
            $v = ($v <= 0.0031308) ? 12.92 * $v : 1.055 * pow($v, 1.0 / 2.4) - 0.055;
            $hex .= sprintf("%02x", (int)round($v * 255));
        }
        return $hex;
    }
}

/** Конвертирует HEX цвет в массив CIE XY координат.
* @param string $hex HEX цвет (#RGB или #RRGGBB)
* @return array ['x' => float, 'y' => float]
*/
if (!function_exists('hexToXy')) {
    function hexToXy($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $r = ($r > 0.04045) ? pow(($r + 0.055) / 1.055, 2.4) : $r / 12.92;
        $g = ($g > 0.04045) ? pow(($g + 0.055) / 1.055, 2.4) : $g / 12.92;
        $b = ($b > 0.04045) ? pow(($b + 0.055) / 1.055, 2.4) : $b / 12.92;
        $X = $r * 0.664511 + $g * 0.154324 + $b * 0.162028;
        $Y = $r * 0.283881 + $g * 0.668433 + $b * 0.047685;
        $Z = $r * 0.000088 + $g * 0.072310 + $b * 0.986039;
        $sum = $X + $Y + $Z;
        if ($sum == 0) return ['x' => 0.3127, 'y' => 0.3290];
        return ['x' => (float)round($X / $sum, 4), 'y' => (float)round($Y / $sum, 4)];
    }
}

/** Масштабирует процентное значение (0-100) в рабочий диапазон устройства.
* @param int|float $level Значение в процентах (0-100)
* @param int $min Минимальный порог устройства (дефолт 0)
* @param int $max Максимальный порог устройства (дефолт 254)
* @param int $limitMin Минимальное возвращаемое значение (дефолт 0)
* @return int
*/
if (!function_exists('levelToWork')) {
    function levelToWork($level, $min = 0, $max = 254, $limitMin = 0) {
		if ($max == $min) return null;
        $level = max(0, min(100, (float)$level));
        $result = (int)round($min + ($max - $min) * $level / 100);
        $result = max($limitMin, $result);
        return (int)max($min, min($max, $result));
    }
}

/** Масштабирует рабочее значение устройства в процентное (0-100).
* @param int|float $val Текущее рабочее значение устройства
* @param int $min Минимальный порог устройства (дефолт 0)
* @param int $max Максимальный порог устройства (дефолт 254)
* @param int $limitMin Минимальное возвращаемое значение в % (дефолт 0)
* @return int
*/
if (!function_exists('workToLevel')) {
    function workToLevel($val, $min = 0, $max = 254, $limitMin = 0) {
        if ($max == $min) return null;
        $percent = ($val - $min) / ($max - $min) * 100;
        $result = (int)round($percent);
        $result = max($limitMin, $result);
        return (int)max(0, min(100, $result));
    }
}

/** Вычисляет новое время с поправкой.
 * dimmerTime($time, $addTime, $sign) 
 * @param string $time Исходное время HH:MM
 * @param string $addTime Коррекция HH:MM
 * @param int $sign 1=прибавить, 0=вычесть
 * @return string Скорректированное время HH:MM
 */
if (!function_exists('dimmerTime')) {
	function dimmerTime($time, $addTime, $sign=1) {
		$modifier = ($sign?'+':'-') . str_replace(':',' hours ',$addTime) . ' minutes';
		return date('H:i', strtotime("$time $modifier"));
	}
}

/** Запускает таймер автоотключения лампы.
 * autoOff($object, $timer, $flag, $presence)
 * @param object|string $object Объект лампы или его имя
 * @param string|int $timer Свойство таймера или конкретное число (сек)
 * @param string|int $flag Флаг блокировки авто режима
 * @param string|int $presence Свойство датчика присутствия
 */
if (!function_exists('autoOff')) {
	function autoOff($object, $timer='timerOff', $flag='flag', $presence='presence') {
		$object = is_object($object)?$object:(is_string($object)?getObject($object):null);
		if(!$object) return;

		$name = $object->object_title;
		$timerValue=120; $flagValue=0; $presenceValue=0;

		if(is_string($timer)) $timerValue=(int)($object->getProperty($timer)??120);
		elseif(is_numeric($timer)) $timerValue=(int)$timer;

		if(is_string($flag)) $flagValue=$object->getProperty($flag)??0;
		elseif(is_numeric($flag)) $flagValue=$flag;

		if(is_string($presence)) $presenceValue=$object->getProperty($presence)??0;
		elseif(is_numeric($presence)) $presenceValue=$presence;

		if($timerValue===0 || $flagValue===1 || $presenceValue===1) return;

		ClearTimeOut($name.'TimerOff');
		
		$timerCode = "if(!getGlobal('{$name}.{$flag}') && !getGlobal('{$name}.{$presence}')) callMethod('{$name}.turnOff');";
		setTimeOut($name.'TimerOff', $timerCode, $timerValue);
	}
}

/** Инициализирует свойства объекта по умолчанию
 * initDefaults($object, $defaults)
 * @param object $object Объект лампы
 * @param array $defaults Массив ['свойство'=>'значение']
 */
if (!function_exists('initDefaults')) {
	function initDefaults($object, $defaults) {
		foreach($defaults as $prop=>$val) {
			if($object->getProperty($prop)=='') $object->setProperty($prop,$val);
		}
	}
}

/** Определяет оптимальные параметры освещения для устройства (яркость, CCT, цвет,
 * уровень цвета, сцена, режим день/ночь) на основе текущего времени,
 * настроек устройства и режима работы автоматики.
 *
 * Логика работы:
 *  - Если workingBy = 2: используется режим по солнцу (восход/закат + смещения).
 *  - Если workingBy != 3: выбор между "днём" и "ночью" на основе timeBetween().
 *  - Если workingBy = 3: используется датчик освещённости (illuminance).
 *  - Переданные аргументы ($level, $cct, $color...) имеют приоритет перед настройками.
 *
 * Возвращает массив настроек света:
 * [
 *     'level'        => int|null,       // Яркость (0–100 или null)
 *     'cct'          => int|null,       // Температура белого (м.до K)
 *     'color'        => string|null     // HEX или RGB, если устройство цветное
 *     'colorLevel'   => int|null,       // Яркость цветного режима
 *     'sceneName'    => string|null     // Название сцены
 *     'mode'         => int|string|null // Что включать(цвет,белый,сцена)
 * ]
 *
 * @param object $object           Объект MajorDoMo, поддерживающий getProperty() и setProperty().
 * @param int|null $level          Принудительная яркость (приоритетно).
 * @param int|null $cct            Принудительная цветовая температура.
 * @param string|null $color       Принудительный цвет RGB/HEX.
 * @param int|null $colorLevel     Принудительная яркость цветного режима.
 * @param string|null $sceneName   Принудительная сцена.
 * @param int|string|null $mode    Принудительный что включать(цвет,белый,сцена)
 *
 * @return array Ассоциативный массив итоговых параметров освещения.
 */
if (!function_exists('getAutoLevelCct')) {
    function getAutoLevelCct(
        $object,
        $level = null,
        $cct = null,
        $color = null,
        $colorLevel = null,
        $sceneName = null,
        $mode = null
		){
        // Кэшируем свойства, чтобы не дергать getProperty каждый раз
        $p = function($name) use ($object) { return $object->getProperty($name); };
        // Определяем начало дня и ночи
        $dayBegin   = $p('dayBegin');
        $nightBegin = $p('nightBegin');
        if ($p('workingBy') == 2 && $p('sunriseTime') != $p('sunsetTime')) {
            $dayBegin   = dimmerTime($p('sunriseTime'), $p('addTimeSunrise'), $p('signSunrise'));
            $nightBegin = dimmerTime($p('sunsetTime'), $p('addTimeSunset'), $p('signSunset'));
        }
        // Режим работы
        $workingBy  = $p('workingBy');
        $workingDay = $p('workingDay');
        // Итоговые переменные
        $res = [
            'level'        => null,
            'cct'          => null,
            'color'        => null,
            'colorLevel'   => null,
            'sceneName'    => null,
            'mode'         => null
        ];
        // ---------- Режим по освещённости ----------
        if ($workingBy == 3) {
            if ($p('illuminance') <= $p('illuminanceMax')) {
                // Используем ночные настройки
                $res['color']        = $color ?? $p('nightColor');
                $res['colorLevel']   = $colorLevel ?? $p('nightColorLevel');
                $res['level']        = $level ?? $p('nightLevel');
                $res['cct']          = $cct ?? $p('nightCct');
                $res['sceneName']    = $sceneName ?? $p('nightScene');
                $res['mode']         = $mode ?? $p('nightMode') ?? '2';

                $object->setProperty('illuminanceFlag', 1);
            }
            return $res;
        }
        // ---------- Режим по времени ----------
        $isNight = ($workingDay == 2 || $workingDay == 3) && timeBetween($nightBegin, $dayBegin);
        $isDay   = ($workingDay == 1 || $workingDay == 3) && timeBetween($dayBegin, $nightBegin);
        if ($isNight) {
            $suffix = 'night';
        } elseif ($isDay) {
            $suffix = 'day';
        } else {
            return $res; // ничего не подходит
        }
        // Автоматически подставляем day/night
        $res['color']        = $color ?? $p("{$suffix}Color");
        $res['colorLevel']   = $colorLevel ?? $p("{$suffix}ColorLevel");
        $res['level']        = $level ?? $p("{$suffix}Level");
        $res['cct']          = $cct ?? $p("{$suffix}Cct");
        $res['sceneName']    = $sceneName ?? $p("{$suffix}Scene");
        $res['mode']         = $mode ?? $p("{$suffix}Mode") ?? '2';
        return $res;
    }
}

/** Создает меню для объекта рекурсивно.
 * createObjectMenu($objectName, $menuItems);
 * @param string $objectName Имя объекта, к которому привязываются команды.
 * @param array $menuItems Массив элементов меню. Каждый элемент должен быть массивом:
 * 	Формат пункта массива $menuItems= [[$objectName, '', '', '', '', '', '', '', '', '', '', '', [],'']];
 * 
 * 	[
 * 		 0 => 'TITLE',          // Название команды
 * 		 1 => 'LINKED_OBJECT',  // Связанный объект (если пусто, используется $objectName)
 * 		 2 => 'LINKED_PROPERTY',// Свойство объекта
 * 		 3 => 'TYPE',           // Тип команды
 * 		 4 => 'CUR_VALUE',      // Текущее значение
 * 		 5 => 'MIN_VALUE',      // Минимальное значение
 * 		 6 => 'MAX_VALUE',      // Максимальное значение
 * 		 7 => 'STEP_VALUE',     // Шаг изменения
 *  	 8 => 'READ_ONLY',      // Только чтение (0 или 1)
 *  	 9 => 'CODE',           // Произвольный код
 *  	 10 => 'DATA',          // Дополнительные данные
 * 		 11 => 'PRIORITY',      // Приоритет команды
 *  	 12 => [ подменю ],     // Массив подменю (необязательный)
 *  	 13 => 'ICON',          // Иконка
 * 	]
 * 
 * @param int $parentId ID родительской команды. По умолчанию 0 (корень).
 * @param int $insertID Текущий последний ID команд. Используется для рекурсивной вставки.
 * @param int $depth Глубина рекурсии. Используется для определения, когда получать MAX(ID).
 *
 * @return int Возвращает последний использованный ID после вставки всех команд.
 */
if (!function_exists('createCommandsMenu')) {
	function createCommandsMenu($objectName, $menuItems, $parentId = 0, $insertID = 0, $depth = 0){
		// при первом вызове получаем максимальный ID из таблицы
		if ($depth === 0 && $insertID === 0) {
			$data = SQLSelectOne("SELECT MAX(ID) AS MAX_ID FROM commands");
			$insertID = $data['MAX_ID'] ?? 0;
		}

		foreach ($menuItems as $item) {

			$Record = [];
			$Record['ID'] = ++$insertID;
			$Record['PARENT_ID'] = $parentId;
			$Record['PARENT_LIST'] = ($parentId == 0) ? '0' : $parentId;
			
			// параметры
			$Record['TITLE'] = $item[0] ?? '';
			$Record['LINKED_OBJECT'] = $item[1] ?: $objectName;
			$Record['LINKED_PROPERTY'] = $item[2] ?? '';
			$Record['TYPE'] = $item[3] ?? '';
			$Record['CUR_VALUE'] = $item[4] ?? '';
			$Record['MIN_VALUE'] = (float)$item[5] ?? 0;
			$Record['MAX_VALUE'] = (float)$item[6] ?? 0;
			$Record['STEP_VALUE'] = (float)$item[7] ?? '';
			$Record['READ_ONLY'] = (int)$item[8] ?? 0;
			$Record['CODE'] = $item[9] ?? NULL;
			$Record['DATA'] = $item[10] ?? NULL;
			$Record['PRIORITY'] = $item[11] ?? 10;
			$Record['SUB_PRELOAD'] = isset($item[12]) ? 1 : 0;
			$Record['ICON'] = $item[13] ?? '';

			// добавляем команду
			SQLInsert('commands', $Record);

			// если есть подменю — рекурсия
			if (!empty($item[12]) && is_array($item[12])) {
				$firstChildId = $insertID + 1;
				$insertID = createCommandsMenu($objectName, $item[12], $Record['ID'], $insertID, $depth + 1);
				$lastChildId = $insertID;

				// обновляем SUB_LIST у родителя
				$childIds = range($firstChildId, $lastChildId);
				$subList = implode(',', $childIds);
				SQLExec("UPDATE commands SET SUB_LIST = '" . DBSafe($subList) . "' WHERE ID = '" . (int)$Record['ID'] . "'");
			}
		}

		return $insertID;
	}
}

/** Удаляет команды меню по структуре $menuItems,
 * используя TITLE и LINKED_OBJECT, включая вложенные
 * команды по SUB_LIST (рекурсивно).
 *
 * Работает на PHP 7 без предупреждений и ошибок.
 *
 * @param string $objectName
 * @param array  $menuItems
 */
if (!function_exists('deleteCommandsMenu')) {
	function deleteCommandsMenu($objectName, $menuItems)
	{
		$conditions = [];
		$stack = $menuItems;
		// 1. Сбор условий TITLE + LINKED_OBJECT
		while (!empty($stack)) {
			$item = array_pop($stack);
			$title = $item[0] ?? '';
			$linkedObject = $item[1] ?: $objectName;
			if ($title !== '') {
				$conditions[] = sprintf(
					"(TITLE='%s' AND LINKED_OBJECT='%s')",
					DBSafe($title),
					DBSafe($linkedObject)
				);
			}
			// добавляем подменю в стек
			if (!empty($item[12]) && is_array($item[12])) {
				foreach ($item[12] as $child) {
					$stack[] = $child;
				}
			}
		}
		// если нет условий — нечего удалять
		if (empty($conditions)) {
			return;
		}
		// 2. Получаем список ID команд + SUB_LIST
		$where = implode(" OR ", $conditions);
		$rows = SQLSelect("SELECT ID, SUB_LIST FROM commands WHERE {$where}");
		if (empty($rows)) {
			return;
		}
		// 3. Собираем все ID для удаления (сам пункт + его дети)
		$idsToDelete = [];
		foreach ($rows as $r) {
			$id = (int)$r['ID'];
			$idsToDelete[$id] = $id;
			if (!empty($r['SUB_LIST'])) {
				$children = explode(',', $r['SUB_LIST']);
				foreach ($children as $childId) {
					$childId = (int)$childId;
					if ($childId > 0) {
						$idsToDelete[$childId] = $childId;
					}
				}
			}
		}
		// 4. Удаляем все команды одним SQL
		$idList = implode(',', $idsToDelete);
		SQLExec("DELETE FROM commands WHERE ID IN ({$idList})");
	}
}
