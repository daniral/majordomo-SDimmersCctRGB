<?php
/** Обработчик изменения свойств.
 * 
 * Метод выполняет комплексную обработку входящих свойств устройства
 * и отвечает за:
 *
 * --- Цвет Яркость  Теплота ---
 *  • Преобразование входящего цвета: HEX или предустановки (red, blue, lime и т.д.).
 *  • Нормализацию значений цвета яркости и теплоты.
 *  • Формирование строки {"x":__,"y":__} и запись в colorWork.
 *  • Автоматическое включение устройства при изменении color level cct.
 *  • Сохранение последних значений colorSaved levelSaved cctSaved.
 *
 * --- Защита от рекурсий ---
 *  • SOURCE="worksUpdated" — предотвращает циклические обновления.
 *
 * --- Используемые свойства объекта ---
 *  • status           — включено/выключено (0/1)
 *  • level            — текущая яркость (1–100)
 *  • cct              — текущая теплота (1–100)
 *  • color            — текущий HEX-цвет
 *  • levelSaved       — сохранённая яркость
 *  • colorSaved       — сохранённый цвет
 *  • cctSaved         — сохранённая теплота
 *  • workValue        — строка для устройства
 *  • levelMin, levelMax — границы яркости устройства
 *  • cctMin, cctMax     — границы теплоты устройства
 * 
 * --- Параметры входящего события ---
 * @param array $params Ассоциативный массив:
 *      - string $params['PROPERTY']   Имя изменяемого свойства.
 *      - mixed  $params['NEW_VALUE']  Новое значение свойства.
 *      - string $params['SOURCE']     Источник события (защита от рекурсий).
 *
 * Логика обработки:
 *  1. Если SOURCE="worksUpdated" → выход (защита от рекурсий).
 *  2. Преобразование предустановок цвета (red, blue, lime ...).
 *  3. Нормализация числовых значений.
 *  4. Для color/level/cct:
 *        - включение устройства,
 *        - генерация workValue,
 *        - сохранение *_Saved.
 *
 * @return void
 */
//

// --- Дефолтные свойства
$this->callMethod('byDefault');

$value = $params['NEW_VALUE'] ?? null;
$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');
// Считываем границы устройства
$min = ($property=='level') ? $this->getProperty('levelMin') : $this->getProperty('cctMin');
$max = ($property=='level') ? $this->getProperty('levelMax') : $this->getProperty('cctMax');

// --- Защита от рекурсий и неверных данных
if ($source === 'worksUpdated' || is_null($value)) {
    if (is_null($value)) {
        $this->setProperty($property, $this->getProperty($property . 'Saved'), 'worksUpdated');
    }
    return;
}

// --- Обработка Цвет Яркость Теплота ---
if ($property === 'color' || $property === 'level' || $property === 'cct') {
    if ($property === 'level' || $property === 'cct') {
        $presets = [
            'coolest' => 0,
            'cool'    => 33,
            'warm'    => 66,
            'warmest' => 100,
        ];
        if (isset($presets[$value])) {
            $value = $presets[$value];
        }
        // Нормализация входящего значения
        $value = normalizeRange($value, 1, 100, 'number');
        $workValue = levelToWork($value, $min, $max, 1);
        if ($workValue === null) return;
    }

    if ($property === 'color') {
        // --- Преобразование предустановок цвета
        static $transform = [
            'red' => '#ff0000', 'green' => '#00ff00', 'blue' => '#0000ff',
            'white' => '#ffffff', 'yellow' => '#ffff00', 'cyan' => '#00ffff',
            'magenta' => '#ff00ff', 'orange' => '#ffa500', 'purple' => '#800080',
            'pink' => '#ffc0cb', 'lime' => '#00ff00'
        ];
        if (isset($transform[$value])) {
            $value = $transform[$value];
        }
        // Нормализация входящего значения
        $value = normalizeRange($value);
        // Конвертируем в XY и упаковываем в JSON строку
        $workValue = json_encode(hexToXy($value));
    }

    // Авто-включение
    if (!$this->getProperty('status')) {
        $this->setProperty('status', 1);
    }
    
    // Сохраняем для истории и восстановления
    $this->setProperty($property . 'Saved', $value);

    // Синхронизируем значение свойства в MajorDoMo
    if ($value != $this->getProperty($property)) {
        $this->setProperty($property, $value, 'worksUpdated');
    }

    // Отправляем готовую команду на устройство
    $this->setProperty($property . 'Work', $workValue, 'propertysUpdated');
}
