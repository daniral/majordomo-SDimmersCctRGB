# 💡 Dimmer RGB CCT
## Простое устройство для MajorDoMo

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-blue" />
  <img src="https://img.shields.io/badge/MajorDoMo-Device%20Module-green" />
  <img src="https://img.shields.io/badge/Status-Production-success" />
  <img src="https://img.shields.io/badge/Type-Smart%20Lighting-yellow" />
  <img src="https://img.shields.io/badge/Version-1.0-orange" />
</p>
---

## 📘 Описание

**`SDimmersCctRGB2`** — расширяет класс *SControllers* 
> Простое устройство диммируемого освещения для MajorDoMo.  
> Управление Цветом, Яркостью и Теплотой.  
---  
Поддерживает:

🎨 Цвет  
  * Принимает данные от устройства  в формате json {"x":__,"y":__}(можно добавить другие)  конвертирует в RGB.И на оборот.  

💡 Яркость и Теплота  
  * Принимает данные в указаном диапазоне (levelMin - levelMax) конвертирует в проценты(1-100).  
---

# ⚙️ Привязка свойств

| Tuya поле      | Свойство MajorDoMo |
| -------------- | ------------------ |
| `state`        | `status`           |
| `brightness`   | `levelWork`        | 
| `color`        | `colorWork`        | 
| `color_temp`   | `cctWork`          | 
---

## 🎨 Управление цветом и теплотой

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
## Теплота может задаваться:  

✔ В процентах от 1 до 100  

✔ Пресетами  

Используемые имена:  
```
coolest, cool, warm, warmest.  
```

# 🔧 Методы 

| Метод            | Описание                 |
| ---------------- | ------------------------ |
| `setColor`       | Установить цвет          |
| `setLevel`       | Установить яркость       |
| `levelUp`        | Увеличить яркость        |
| `levelDown`      | Уменьшить яркость        |
| `setCct`         | Установить теплоту       |
| `cctUp`          | Увеличить теплоту        |
| `cctDown`        | Уменьшить теплоту        |
---