<?php
/* Описание устройства
# 💡 Dimmer RGB CCT - 2
## Простое устройство для MajorDoMo

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
  <img src="https://img.shields.io/badge/Version-2.0-orange" />
</p>
---

## 📘 Описание

**`SDimmersCctRGB2`** — расширяет класс *SControllers* 
> Простое устройство диммируемого освещения для MajorDoMo.  
> Управление Цветом, Яркостью и Теплотой.  

Поддерживает:

🎨 Цветной свет

💡 Белый свет (яркость + температура CCT)

🕒 Автовключение / автоотключение

👁 Работа по датчику движения / света / времени / солнцу

🌗 Авто-режимы День / Ночь / 24 часа

🧩 Создание/удаление меню управления MajorDoMo

При первом запуске автоматически заполняются все необходимые свойства.

---

# ⚙️ Привязка свойств

| Tuya поле      | Свойство MajorDoMo |
| -------------- | ------------------ |
| `state`        | `status`           |
| `brightness`   | `levelWork`        | 
| `color`        | `colorWork`        | 
| `color_temp`   | `cctWork`          | 

`После привязки свойств надо поизменять свойства из приложения   
    чтобы прилетели данные в объект`.  
---

# 🚦 Обычный режим

Включение лампы:
```php
callMethod('ObjectName.turnOn');
```
Если параметры не указаны — берутся сохранённые (`...Saved`) или значения по умолчанию:

| Параметр          | Если пусто       |
| ----------------- | ---------------- |
| `levelSaved`      | 100              |
| `cctSaved`        | 1                |
| `colorSaved`      | #FFFFFF        |
| `mode`            | 2 (теплый белый) |

Включение с параметрами:
```php
callMethod('Object.turnOn', [  
  'level'      => 1..100,  
  'cct'        => 1..100 или присеты,  
  'color'      => '#RRGGBB' , '#RGB' или присеты,  
  'mode'       => 1 (цветной) или 2 (теплый белый)]);  
```
При обычном включении ставится `flag=1`, блокируя авто-режим.  
**Что бы снять флаг надо запустить метод turnOff**   

---

# 🤖 Авто-режим

Запуск без параметров:
```php
callMethod('Object.turnOn', ['autoMode' => 1]);
//или
callMethod('Object.turnOn', array('autoMode'=>1));
```
Устанавливаются значения которые указаны в свойствах дня и ночи.

Запуск с принудительными параметрами:
```php
callMethod('Object.turnOn', [
  'autoMode'   => 1,
  'level'      => 1..100,
  'cct'        => 1..100 или присеты,
  'color'      => '#RRGGBB' , '#RGB' или присеты,
  'mode'       => 1 (цветной) или 2 (теплый белый)]);
```
Эти значения устанавливаются один раз если вызвать в следующий раз без параметров то установятся те которые указаны в свойствах дня и ночи.
Можно не задавать все значения.
Те которые не заданы установятся из свойств дня и ночи

Особенности:

* Работает таймер `timerOff` — авто-выключение. Если 0 то не выключится.
* Пока `presence=1` — не выключается. При изменении `presence` = 0 — запускается `autoOff()`.
* Три режима работы: **День**, **Ночь**, **24 часа**  
* Три источника автоматизации: **Время**, **Солнце**, **Датчик освещённости**

# Режимы по источнику (`workingBy`)

| Значение | Описание             |
| -------- | -------------------- |
| `1`      | По времени           |
| `2`      | По солнцу            |
| `3`      | По датчику освещения |

### 🌅 По солнцу:

Требует свойства:

* `sunriseTime`
* `sunsetTime`

Можно корректировать:

* `addTimeSunrise` `addTimeSunset` - смещение времени (чч:мм)
* `signSunrise` `signSunset` - направление смещения (0=вычесть, 1=прибавить)

### 💡 По датчику освещения:

Использует:

* свойство `illuminance`
* порог `illuminanceMax`

Не проверялось. Нету датчика.
---

# 🔧 Методы 

## ⛔ Отключение 
```php
callMethod('Object.turnOff');
```
Сбрасывает: `flag=0` `illuminanceFlag = 0`

---
## 🔁 Переключение
```php
callMethod('Object.switch');
```
Поведение:

* Если лампа **в авто-режиме** — включит сохранённые значения.
* Если **выключена** — включит сохранённые значения.
* Если **включена вручную** — выключит.
---
## 🎨 Управление цветом и яркостью цвета

## Цвет может задаваться:

✔ HEX-кодами

* `#RRGGBB`
* `#RGB`

✔ Цветовыми пресетами

Используемые имена:
```
red, green, blue, white, yellow,
cyan, magenta, orange, purple,
pink, lime
```

```php
callMethod('Имя Объекта.setColor', array("value"=>`#RRGGBB` или `#RGB` или присет));
```

---
## 💡 Управление белым светом (Яркость)

| Метод       | Описание           |
| ----------- | ------------------ |
| `setLevel`  | Установить яркость |
| `levelDown` | Уменьшить          |
| `levelUp`   | Увеличить          |

```php
callMethod('Имя Объекта.setLevel', array("value"=>1--100));
callMethod('Имя Объекта.levelUp', array("value"=>1--100));
  *callMethod('Имя Объекта.levelUp'); увеличит на 10
callMethod('Имя Объекта.levelDown', array("value"=>1--100));
  *callMethod('Имя Объекта.levelDown'); уменьшит на 10
```

---
## 🔥 Управление белым светом (теплота)

| Метод     | Описание               |
| --------- | ---------------------- |
| `setCct`  | Установить температуру |
| `cctDown` | Уменьшить              |
| `cctUp`   | Увеличить              |

```php
callMethod('Имя Объекта.setCct', array("value"=>1--100 или присет));
  *Присеты - `coolest`, `cool`, `warm`, `warmest`
callMethod('Имя Объекта.cctUp', array("value"=>1--100));
  *callMethod('Имя Объекта.cctUp'); увеличит на 10
callMethod('Имя Объекта.cctDown', array("value"=>1--100));
  *callMethod('Имя Объекта.cctDown'); уменьшит на 10
```
---
## 🚨 Блокировка авто-режима
      * Все методы → `flag=1`.

---

## 🧩 Меню управления

Создать меню:
```php
callMethod('Object.createCommandsMenu');
```

Удалить меню:
```php
callMethod('Object.deleteCommandsMenu');
```

---
*/


/** PhpDoc
/**
 * Метод включения устройства (turnOn).
 * * Поддерживает два режима работы: ручной (с установкой флага блокировки) 
 * и автоматический (по датчикам/времени).
 *
 * @param array $params {
 * @type int|bool $autoMode  Если 1, запускает логику автоматического управления.
 * @type int      $level     Яркость (1-100). Если не задана, берется Saved или дефолт.
 * @type string   $color     Цвет (HEX или название).
 * @type int      $cct       Цветовая температура (1-100).
 * @type int      $mode      Режим работы: 1 - Color (RGB), 2 - White (CCT).
 * }
 * 
 * @property int    $status      Статус питания (0/1).
 * @property int    $flag        Флаг ручного управления (блокирует авто-режим).
 * @property int    $timerOff    Таймер автовыключения (сек).
 * @property string $workingBy   Тип автоматизации (1-время, 2-солнце, 3-датчик).
 * @return void
 *
 * @note
 */
//

// --- Дефолтные свойства
$this->callMethod('byDefault');

$autoMode = ($params['autoMode'] ?? 0) == 1;
$colorSaved = $this->getProperty('colorSaved');
$levelSaved = $this->getProperty('levelSaved');
$cctSaved = $this->getProperty('cctSaved');
$color = normalizeRange($params['color']) ?? (!$autoMode ? ($colorSaved ?? '#FFFFFF') : null);
$level = normalizeRange($params['level'], 1, 100, 'number') ?? (!$autoMode ? ($levelSaved ?? 100) : null);
$cct = normalizeRange($params['cct'], 1, 100, 'number') ?? (!$autoMode ? ($cctSaved ?? 50) : null);
$mode = $params['mode'] ?? (!$autoMode ? ($this->getProperty('mode') ?? '1') : null);

// --- Обычный режим (без авто)
if (!$autoMode) {
    if($mode == 1){
      $this->setProperty('color', $color, 'noAutoMode');
      $this->setProperty('level', $level, 'noAutoMode');
    }elseif($mode == 2){
      $this->setProperty('cct', $cct, 'noAutoMode');
      $this->setProperty('level', $level, 'noAutoMode');
    }
}
// --- Авто режим 
if ($autoMode && !$this->getProperty('flag')) {
  $levels = getAutoLevelCct($this, $level, $cct, $color, null, null, $mode);
  if($levels['mode'] !== null){
    if($levels['mode'] == 1 && $levels['level'] !== null && $levels['color'] !== null){
      $this->setProperty('level', $levels['level'], 'autoMode');
      $this->setProperty('color', $levels['color'], 'autoMode');
    }elseif($levels['mode'] == 2 && $levels['level'] !== null && $levels['cct'] !== null){
      $this->setProperty('level', $levels['level'], 'autoMode');
      $this->setProperty('cct', $levels['cct'], 'autoMode');
    }
    // --- Авто-выключение
	  if ((int)$this->getProperty('timerOff') > 0) autoOff($this);
  }
}