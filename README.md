# 💡 Dimmer RGB
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

**`SDimmersCctRGB`** — расширяет класс *SControllers* 
> Простое устройство диммируемого освещения для MajorDoMo.

---  
Поддерживает:

🎨 Цвет

💡 Яркость

---

# ⚙️ Привязка свойств

| Tuya поле      | Свойство MajorDoMo |
| -------------- | ------------------ |
| `state`        | `status`           |
| `brightness`   | `levelWork`        | 
| `color`        | `colorWork`        | 
---


# 🔧 Методы 

## 🎨 Управление цветом и яркостью

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

| Метод            | Описание                 |
| ---------------- | ------------------------ |
| `setColor`       | Установить цвет          |
| `setLevel`       | Установить яркость цвета |
| `levelUp`        | Увеличить яркость        |
| `levelDown`      | Уменьшить яркость        |
---