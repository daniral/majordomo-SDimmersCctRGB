<?php

/**
 * Переключает состояние лампы (включить/выключить).
 *
 * Логика:
 * 1. Если лампа включена в авто-режиме (`flag=0` и `status=1`) — включает сохранённые значения
 *    яркости (`levelSaved`) и цвета (`colorSaved`).
 * 2. Если лампа выключена — включает сохранённые значения (`levelSaved` и `colorSaved`).
 * 3. Если лампа включена не в авто-режиме — выключает её.
 *
 * @return void
 */

$status = (int)$this->getProperty('status');
$flag   = (int)$this->getProperty('flag');

if ($flag && $status) {
    $this->callMethod('turnOff');
} else {
    $this->callMethod('turnOn');
}