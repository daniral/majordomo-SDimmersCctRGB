<?php

/**
 * ============================================================
 * Dictionary for SDimmersCctRGB2 control
 * ============================================================
 *
 * $dictionary - array defining patterns to recognize commands:
 *   - 'SDimmersCctRGB2_PATTERN_BRIGHTNESS': keywords for brightness control
 *   - 'SDimmersCctRGB2_PATTERN_TEMPERATURE': keywords for color temperature control
 *   - 'SDimmersCctRGB2_PATTERN_COLOR': keywords for color control
 *
 * Each value is a string with keywords separated by |
 * Constants with the LANG_ prefix are defined for each key
 *   e.g., LANG_SDimmersCctRGB2_PATTERN_BRIGHTNESS
 * These constants are used to recognize text or voice commands.
 */
$dictionary = array(

    // Brightness control
    'SDimmersCctRGB2_PATTERN_BRIGHTNESS' => 'bright|brightness|lighter|dimmer|light level|increase light|decrease light',

    // Color temperature control
    'SDimmersCctRGB2_PATTERN_TEMPERATURE' => 'temperature|color|warm|cool|cold|neutral|tone|whit|yellow|blue',

    // Color control
    'SDimmersCctRGB2_PATTERN_COLOR' => 'red|green|blue|white|yellow|cyan|magenta|orange|purple|pink|lime',

);

foreach ($dictionary as $k => $v) {
    if (!defined('LANG_' . $k)) {
        @define('LANG_' . $k, $v);
    }
}
