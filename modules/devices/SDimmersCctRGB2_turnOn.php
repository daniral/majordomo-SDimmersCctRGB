<?php
/* Описание устройства
# 💡 Dimmer CCT RGB
### Простое устройство для MajorDoMo  

Модуль добавляет поддержку диммируемого освещения. 

---  
Расширяет встроенный класс `SControllers`, добавляя новый класс **`SDimmersCctRGB2`**.  
С авто режимом включеня по датчику освещения, восходу/закату солнца или по установленному времени.  
С заданными цветом, яркостью и температурой для дня и ночи.  
Автовыключение через заданное времени.  
Авто режим для Дня, Ночи или круглосуточно.  

---  

## ⚙️ Привязка свойств  

| Свойство Tuya  | Свойство в MajorDoMo | Описание                                    | 
|----------------|----------------------|-------------------------------------------- | 
| `state`        | `status`             | Включение / выключение лампы                | 
| `brightness`   | `levelWork`          | Рабочая яркость                             | 
| `color`        | `colorWork`          | Рабочий цвет                                | 

`После привязки свойств надо поизменять свойства из приложения   
    чтобы прилетели данные в объект`.  
---

### **ОБЫЧНЫЙ РЕЖИМ:**  

Включить - callMethod('имя объекта '.'turnOn');  
- Без параметров : 
  - colorSaved       если не заполнено - #ffff00  
  - levelSaved       если не заполнено - 100  
  
- С параметрами:  
  - callMethod('имя объекта.turnOn', array('level'=> '1<-->100',   
                                           'color'=> '#RRGGBB' или '#RGB' или присет,  
                                           )); 
										   
  - Пример: callMethod('имя объекта.turnOn',array('level'=> '50',   
												                          'color'=> '#FFFF00',  
											                            ));  
  **Устанавливается flag=1. Стопер который не дает запускаться авто режиму.**  
  **Что бы снять флаг надо запустить метод turnOff**  

### **АВТО РЕЖИМ:**  

Включить авто режим - callMethod('имя объекта.turnOn', array('autoMode'=>1)) 
к пимеру запускать по датчику движения;   
- Включится на время которое указано в timerOff(сек). Если 0 то включится но сам не выключится.  
- Если в presence(например данные с датчика присутствия) 1 то не выключится.  
  - Как только в presence изменися с 1 на 0 запустится автовыключение(autoOff).    
- Авто режим для Дня, Ночи или Круглосуточно.   
  - в workingDay:   
    + 1 - День  (дневные установки)  
    + 2 - Ночь  (ночные установки)  
    + 3 - Круглосуточно.(Ночью ночные установки яркости и теплоты. Днем дневные.)  

- Авто режим по времени (workingBy=1):  
    - после начало ночь - ночные установки яркости и теплоты.  
    - после начло день - дневные.  
- Авто режим по солнцу (workingBy=2):  
  - после захода - ночные установки .  
  - после восхода - дневные установки.  
  - Надо обязательно писать в свойства sunriseTime и sunsetTime время восхода и заката.  
    Если не указано, то то, что указано - по времени.  
  - К восходу и закату можно прибавить или отнять время если надо чтобы включалось или выключалось раньше или позже:  
    - addTimeSunrise - к рассвету в формате 05:30 (5 часов 30 минут)  
      - signSunrise 0 - отнять 1 - прибавить.  
    - addTimeSunset  - к закату в формате 00:30 (30 минут)  
      - signSunset 0 - отнять 1 - прибавить.  
- Авто режим по датчику (workingBy=3):  
    - Только ночные установки яркости и теплоты.  
    - В свойство illuminance надо писать данные с датчика освещения.  
      - если illuminance меньше чем установленно в illuminanceMax подсветка включится.  
    - ***Работу по датчику освещения не проверял так как не имеется в наличии.***   
- **Можно запустить авто режим с параметрами:**  
    - callMethod('имя объекта.turnOn', array('autoMode'=>1,  
                                             'level'=> '1<-->100',   
                                             'color'=> '#RRGGBB' или '#RGB' или присет,  
											                       ));  


## **🔧 МЕТОДЫ:**  

- **turnOff**  
  - Выключить - callMethod('имя объекта '.'turnOff');  
    - Устанавливается flag = 0, illuminanceFlag = 0  
- **switch**  
 - Переключить - callMethod('имя объекта '.'switch');  
    - Если лампа включена в авто-режиме  
      — включает сохранённые значения.  
    - Если лампа выключена  
      — включает сохранённые значения.  
    - Если лампа включена не в авто-режиме 
      — выключает её.  

- **setColor**   
  - Установить цвет.(array("value"=> '#RRGGBB' или '#RGB' или присет))  
    - Значение цвета может быть:  
      - шестнадцатеричным кодом формата `#RRGGBB`  или '#RGB';  
      - именем предустановленного цвета (присет):  
          + 'red', 'green', 'blue', 'white', 'yellow', 'cyan', 
          + 'magenta', 'orange', 'purple', 'pink', 'lime'.  
    - **flag=1** - авто режим и автовыключение не запустится.  
- **setLevel**   
  - Установить яркость цвета.(array("value"=> 1 <--> 100 %))  
    - **flag=1** - авто режим и автовыключение не запустится.  
- **levelDown**  
  - Уменьшить яркость цвета.(array("value"=>1--100)). Без  параметров -10.  
  - **flag=1** - авто режим и автовыключение не запустится.  
- **levelUp**  
  - Увеличить яркость цвета.(array("value"=>1--100)). Без  параметров 10.  
  - **flag=1** - авто режим и автовыключение не запустится.  

- **byDefault**  
  - Установит параметры по дефолту. Это если что-то пошло не так.  
    (При первом запуске метода turnOn тоже все выставится по дефолту.)  
- **createCommandsMenu**  
  - Создаст меню данного объекта в **Меню Управления**   
        Где можно все удобно настроить.  
        Меню будет называться по имени объекта. При желании можно изменить на любое другое. 
- **deleteCommandsMenu**  
  - Удалит меню данного объекта в "Меню Управления"  

При первом запуске метода **turnOn** все нужные свойства 
для работы устройства должны прописаться сами.  
*/


/** PhpDoc
 *
 * @param array $params Массив входных параметров для управления лампой:
 *   - bool|int    $params['autoMode']    Включение авто режима (1 — включен, 0 — выключен).
 *   - int|null    $params['level']       Уровень яркости (1–100).
 *   - string|null $params['color']       Цвет в HEX формате ('#FFFFFF').
 *
 * @property string $color         Текущий цвет лампы (HEX), сохраняется при режиме color.
 * @property int    $level         Уровень яркости белого света (1–100).
 * @property bool   $autoMode      Флаг авто режима.
 * @property bool   $flag          Внутренний флаг для авто режима (защита вызова).
 * @property int    $timerOff      Время авто-выключения (в секундах), 0 — отключено.
 *
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