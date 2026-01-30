<?php
/**
 * Устанавливает цветовую температуру лампы.
 *   
 * Принимает массив параметров с ключом 'value', где указана температура в процентах
 * или один из строковых пресетов:
 *   - 'coolest'
 *   - 'cool'
 *   - 'warm'
 *   - 'warmest'
 *
 *  callMethod('имя объекта.setCct', array("value"=>1--100));
 *  callMethod('имя объекта.setCct', array("value"=>'coolest'));
 * 
 * @param array{
 *     value: int|string|null   // Цветовая температура 1–100% или строковый пресет
 *     cct:   int|string|null   // Цветовая температура 1–100% или строковый пресет
 * } $params Ассоциативный массив параметров.
 *
 * @return void
 */

$cct = $params['cct'] ?? $params['value'] ?? null;
if ($cct === null) return;

$this->setProperty('cct', $cct, 'setCct');
