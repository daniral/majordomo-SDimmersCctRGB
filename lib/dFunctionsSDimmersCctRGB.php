<?php
/** Краткое описание всех функций
 *
 * normalizeRange($val, $min, $max, $type) — Проверяет и нормализует значение (число или HEX).
 * adjustProperty($obj, $property, $value, $direction, $defaultStep, $min, $max) — Увеличение/уменьшение свойств.
 * xyToHex($x, $y) — Конвертирует CIE XY координаты в HEX цвет.
 * hexToXy($hex) — Конвертирует HEX цвет в массив CIE XY координат.
 * levelToWork($level, $min, $max, $limitMin) — Масштабирует проценты (0-100) в рабочий диапазон устройства.
 * workToLevel($val, $min, $max, $limitMin) — Масштабирует рабочее значение устройства в проценты (0-100).
 * *--------------------------------------------------------------------------------------------
 *| Функция            | Назначение                                                           |
 *| ------------------ | ---------------------------------------------------------------------|
 *| `normalizeRange`   | Нормализует HEX или число в диапазон                                 |
 *| `adjustProperty`   | Универсальное изменение свойства лампы                               |
 *| `xyToHex`          | CIE XY координаты -> HEX цвет (#RRGGBB)                              |
 *| `hexToXy`          | HEX цвет -> массив ['x' => ..., 'y' => ...]                          |
 *| `levelToWork`      | Проценты (0-100) -> Рабочий диапазон (min..max)                      |
 *| `workToLevel`      | Рабочий диапазон (min..max) -> Проценты (0-100)                      |
 *--------------------------------------------------------------------------------------------
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