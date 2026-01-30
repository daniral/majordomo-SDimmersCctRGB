<?php

/**
 * Устанавливает свойства объекта лампы по умолчанию.
 *
 * Устанавливаются следующие свойства:
 *
 * --- Основные параметры ---
 * @property string  $color            Цвет лампы в формате HEX (например "#ffff00")
 * @property string  $level            Яркость (1–100)
 * @property string  $cct              Температура цвета (1–100)
 * @property int     $levelMin         Минимальная рабочая яркость
 * @property int     $levelMax         Максимальная рабочая яркость
 * @property int     $cctMin           Минимальная рабочая температура цвета
 * @property int     $cctMax           Максимальная рабочая температура цвета
 * @property int     $mode             Режим работы (1=цвет, 2=температура)
 * --- Дневной режим ---
 * @property string  $dayLevel         Яркость днём (1–100)
 * @property string  $dayColor         Цвет днём (HEX)
 * @property string  $dayCct           Температура цвета днём (1–100)
 * @property int     $dayMode          Режим работы днём (1=цвет, 2=температура)
 * --- Ночной режим ---
 * @property string  $nightLevel       Яркость ночью (0–100)
 * @property string  $nightColor       Цвет ночью (HEX)
 * @property string  $nightCct         Температура цвета ночью (1–100)
 * @property int     $nightMode        Режим работы ночью (1=цвет, 2=температура)
 * --- Автоматизация ---
 * @property string  $timerOff         Время авто-выключения лампы (секунды)
 * @property string  $autoOnOff        Автоматическое управление включением (0/1)
 * @property string  $presence         Состояние датчика присутствия (0/1)
 * @property string  $flag             Внутренний флаг, блокирующий авто-режим (0/1)
 *
 * --- Освещённость ---
 * @property string  $illuminance      Текущий уровень освещения
 * @property string  $illuminanceMax   Максимальный уровень освещения для авто-режима
 * @property string  $illuminanceFlag  Включение работы по освещённости (0/1)
 *
 * --- Режимы работы ---
 * @property string  $workingDay       Режим работы лампы (1=день, 2=ночь, 3=круглосуточно)
 * @property string  $workingBy        Источник автоматизации (1=по времени, 2=по солнцу, 3=по датчику)
 *
 * --- Время дня и ночи ---
 * @property string  $dayBegin         Время начала дневного режима (чч:мм)
 * @property string  $nightBegin       Время начала ночного режима (чч:мм)
 *
 * --- Данные о солнце ---
 * @property string  $sunriseTime      Время восхода (чч:мм)
 * @property string  $sunsetTime       Время заката (чч:мм)
 * @property string  $addTimeSunrise   Смещение времени восхода (чч:мм)
 * @property string  $addTimeSunset    Смещение времени заката (чч:мм)
 * @property string  $signSunrise      Направление смещения восхода (0=вычесть, 1=прибавить)
 * @property string  $signSunset       Направление смещения заката (0=вычесть, 1=прибавить)
 *
 * @return void
 */


$defaults = [
    'color' => '#ffffff', 
    'level' => '100',
    'cct' => '100',
    'mode' => '2',

    'levelMin' => '1',
    'levelMax' => '254',
    'cctMin' => '153',
    'cctMax' => '500',

    'dayColor' => '#FFFFFF',
    'dayLevel' => '100', 
    'dayCct' => '100', 
    'dayMode' => '2',

    'nightColor' => '#FFFF00',
    'nightLevel' => '30', 
    'nightCct' => '1', 
    'nightMode' => '2',
    
    'timerOff' => '45', 'autoOnOff' => '1',
    'presence' => '0', 'flag' => '0',
    'illuminance' => '0', 'illuminanceMax' => '0', 'illuminanceFlag' => '0',
    'workingDay' => '2', 'workingBy' => '1',
    'dayBegin' => '08:00', 'nightBegin' => '18:00',
    'sunriseTime' => '08:00', 'sunsetTime' => '18:00',
    'addTimeSunrise' => '00:00', 'addTimeSunset' => '00:00',
    'signSunrise' => '1', 'signSunset' => '1'
];
initDefaults($this, $defaults);