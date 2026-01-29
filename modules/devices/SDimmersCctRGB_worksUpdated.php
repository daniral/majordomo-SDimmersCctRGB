<?php
/**
 * 
 * Обрабатывает изменение свойств устройства, связанных с цветом и яркостью.
 *
 * Функция выполняет следующие задачи:
 *
 *  1. Обрабатывает изменения свойства "levelWork":
 *      - Получает новое значение levelMin - levelMax.
 *      - Конвертирует его в % 1-100.
 *      - Устанавливает уровень яркости (level)
 * 
 *  2. Обрабатывает изменения свойства "colorWork":
 *      - Получает новое значение {"x":<value>,"y":<value>}.
 *      - Конвертирует его в RGB Hex.
 *      - Устанавливает цвет (color)
 *
 * Входные параметры:
 * -------------------
 * @param array $params Ассоциативный массив, содержащий:
 *      - 'NEW_VALUE'   (mixed)  Новое значение изменённого свойства.
 *      - 'SOURCE'      (string) Источник изменения свойства.
 *      - 'PROPERTY'    (string) Имя свойства, которое изменилось.
 *
 * Важные свойства объекта:
 * ------------------------
 * - color             — текущий HEX-цвет устройства.
 * - colorSaved        — последний сохранённый HEX-цвет.
 * - level             — уровень яркости (1–100).
 * - levelSaved        — последний сохранённый уровень яркости.
 *
 * Используемые функции:
 * ----------------------
 * - normalizeRange($val, $min, $max, $type) — нормализует числовое значение.
 *
 * Примечания:
 * -----------
 * - Обработка не выполняется, если SOURCE == 'propertysUpdated'
 *   (во избежание рекурсии).
 *
 * @return void
 */
//

// --- Дефолтные свойства
if($this->getProperty('color') == '') $this->setProperty('color', '#ffffff');
if($this->getProperty('level') == '') $this->setProperty('level', 100);
if($this->getProperty('levelMin') == '') $this->setProperty('levelMin', 1);
if($this->getProperty('levelMax') == '') $this->setProperty('levelMax', 254);

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
$levelMin = (int)$this->getProperty('levelMin');
$levelMax = (int)$this->getProperty('levelMax');

// Для цвета оставляем сырой JSON/массив, для яркости нормализуем
$value = ($property === 'colorWork')
    ? $params['NEW_VALUE']
    : normalizeRange($params['NEW_VALUE'], $levelMin, $levelMax, 'number');

// Защита от рекурсий (если изменение пришло от нашего же скрипта управления)
if ($source === 'propertysUpdated' || is_null($value)) return;

// --- БЛОК ЦВЕТА (XY от устройства -> HEX в интерфейс) ---
if ($property === 'colorWork') {
    $data = is_array($value) ? $value : json_decode($value, true);
    if (!$data || !isset($data['x']) || !isset($data['y'])) return;
    
    // Используем вашу функцию из файла
    $hex = xyToHex($data['x'], $data['y']);
    
    // Обновляем визуальные свойства с источником worksUpdated
    $this->setProperty('color', $hex, 'worksUpdated');
    $this->setProperty('colorSaved', $hex);
    return;
}

// --- БЛОК ЯРКОСТИ (Рабочее значение -> Проценты 1-100) ---
if ($property === 'levelWork') {
    // Конвертируем значение устройства в % (с учетом min/max и лимитом 1%)
    $level = workToLevel($value, $levelMin, $levelMax, 1);
    
    if (is_null($level)) {
        $level = (int)$this->getProperty('levelSaved') ?: 100;
    }

    // Обновляем текущее состояние в интерфейсе
    $this->setProperty('level', $level, 'worksUpdated');
    $this->setProperty('levelSaved', $level);
    return;
}