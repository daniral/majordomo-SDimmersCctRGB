<?php
/** Обработчик изменения свойств.
 * 
 * Метод выполняет комплексную обработку входящих свойств устройства
 * и отвечает за:
 *
 * --- Цвет и яркость ---
 *  • Преобразование входящего цвета: HEX или предустановки (red, blue, lime и т.д.).
 *  • Нормализацию значений цвета и яркости.
 *  • Формирование строки {"x":__,"y":__} и запись в colorWork.
 *  • Автоматическое включение устройства при изменении color или level.
 *  • Сохранение последних значений colorSaved и levelSaved.
 *
 * --- Защита от рекурсий ---
 *  • SOURCE="worksUpdated" — предотвращает циклические обновления.
 *  • SOURCE="autoMode" — отключает запись в flag и сохранение значений.
 *
 * --- Используемые свойства объекта ---
 *  • status           — включено/выключено (0/1)
 *  • level            — текущая яркость (1–100)
 *  • color            — текущий HEX-цвет
 *  • levelSaved       — сохранённая яркость
 *  • colorSaved       — сохранённый цвет
 *  • workValue        — строка для устройства
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
 *  4. Для color/level:
 *        - включение устройства,
 *        - генерация workValue,
 *        - сохранение *_Saved.
 *
 * @return void
 */
//

// --- Дефолтные свойства
if($this->getProperty('color') == '') $this->setProperty('color', '#ffffff');
if($this->getProperty('level') == '') $this->setProperty('level', 100);
if($this->getProperty('levelMin') == '') $this->setProperty('levelMin', 1);
if($this->getProperty('levelMax') == '') $this->setProperty('levelMax', 254);

$value = $params['NEW_VALUE'] ?? null;

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

$property = $params['PROPERTY'] ?? null;
$source   = strtok($params['SOURCE'] ?? '', ' ');

// 1. Считываем границы устройства
$levelMin = $this->getProperty('levelMin');
$levelMax = $this->getProperty('levelMax');

// 2. Нормализация входящего значения
$value = ($property === 'color')
    ? normalizeRange($value) 
    : normalizeRange($value, 1, 100, 'number');

// --- Защита от рекурсий и неверных данных
if ($source === 'worksUpdated' || is_null($value)) {
    if (is_null($value)) {
        $this->setProperty($property, $this->getProperty($property . 'Saved'), 'worksUpdated');
    }
    return;
}

// --- Обработка Цвет и Яркость ---
if ($property === 'color' || $property === 'level') {
    
    // Подготовка значения для устройства (Work)
    if ($property === 'level') {
        $workValue = levelToWork($value, $levelMin, $levelMax, 1);
        if ($workValue === null) return;
    } 
    
    if ($property === 'color') {
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
    return;
}
