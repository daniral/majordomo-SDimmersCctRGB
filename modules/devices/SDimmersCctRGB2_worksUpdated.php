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
 *  *  1. Обрабатывает изменения свойства "cctWork":
 *      - Получает новое значение cctMin - cctMax.
 *      - Конвертирует его в % 1-100.
 *      - Устанавливает уровень теплоты (cct)

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
 * - colorWork         — текущее рабочее значение цвета (JSON {"x":__,"y":__}).
 * - colorSaved        — последний сохранённый HEX-цвет.
 * - level             — уровень яркости (1–100).
 * - levelWork         — текущее рабочее значение яркости (число).
 * - levelSaved        — последний сохранённый уровень яркости.
 * - cct               — уровень теплоты (1–100).
 * - cctWork           — текущее рабочее значение теплоты (число).
 * - cctSaved          — последний сохранённый уровень теплоты.
 *
 * Используемые функции:
 * ----------------------
 * - normalizeRange($val, $min, $max, $type) — нормализует числовое значение.
 * - workToLevel($workValue, $min, $max, $limitMin) — конвертирует рабочее значение в %.
 * - xyToHex($x, $y) — конвертирует XY-координаты в HEX-цвет.
 * 
 * Логика обработки:
 * -------------------
 * 1. Получает имя изменённого свойства из $params['PROPERTY'].
 * 2. Получает источник изменения из $params['SOURCE'].
 * 3. В зависимости от изменённого свойства выполняет соответствующую обработку:
 *   - Для "colorWork":
 *      • Декодирует JSON-строку в массив.
 *     • Конвертирует XY в HEX.
 *    • Устанавливает свойства color и colorSaved.
 *  - Для "levelWork":
 *     • Получает levelMin и levelMax.
 *    • Конвертирует рабочее значение в %.
 *   • Устанавливает свойства level и levelSaved.
 * - Для "cctWork":
 *    • Получает cctMin и cctMax.
 *   • Конвертирует рабочее значение в %.
 *  • Устанавливает свойства cct и cctSaved.
 * 4. Защита от рекурсий:
 *  • Если источник изменения равен "propertysUpdated", функция завершает выполнение без изменений.
 * 
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
$this->callMethod('byDefault');

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
$max   = $property === 'levelWork' ? $this->getProperty('levelMax') : $this->getProperty('cctMax');
$min   = $property === 'levelWork' ? $this->getProperty('levelMin') : $this->getProperty('cctMin');
$valueToSet = null;

// Для цвета оставляем сырой JSON/массив, для яркости нормализуем
$value = ($property === 'colorWork')
    ? $params['NEW_VALUE']
    : normalizeRange($params['NEW_VALUE'], $min, $max, 'number');
    
// Защита от рекурсий (если изменение пришло от нашего же скрипта управления)
if ($source === 'propertysUpdated' || is_null($value)) return;

// --- БЛОК ЦВЕТА (XY от устройства -> HEX в интерфейс) ---
if ($property === 'colorWork') {
    $data = is_array($value) ? $value : json_decode($value, true);
    if (!$data || !isset($data['x']) || !isset($data['y'])) return;
    
    // Используем функцию конвертации XY в HEX
    $valueToSet = xyToHex($data['x'], $data['y']);
}

// --- БЛОК ЯРКОСТИ И ТЕПЛОТЫ (Рабочее значение -> Проценты 1-100) ---
if ($property === 'levelWork' || $property === 'cctWork') {
    // Конвертируем значение устройства в % (с учетом min/max и лимитом 1%)
    $valueToSet = workToLevel($value, $min, $max, 1);
}

// Если не удалось рассчитать значение — выходим
if (is_null($valueToSet)) return;

// Записываем полученное значение в основное свойство и сохраняем
$this->setProperty(str_replace('Work', '', $property), $valueToSet, 'worksUpdated');
$this->setProperty(str_replace('Work', '', $property) . 'Saved', $valueToSet);