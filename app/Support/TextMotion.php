<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * A text's motion settings, as stored in a block's data under motion.{key}.
 * Off unless `enabled`; everything else falls back to DEFAULTS. Played by
 * resources/js/motions/text.js.
 */
class TextMotion
{
    public const DEFAULTS = [
        'from' => 'bottom',
        'distance' => 24,
        'split' => 'none',
        'stagger' => 0.06,
        'duration' => 0.8,
        'delay' => 0.0,
        'ease' => 'soft',
        'mask' => false,
    ];

    private const CHOICES = [
        'from' => ['bottom', 'top', 'left', 'right'],
        'split' => ['none', 'lines', 'words', 'chars'],
        'ease' => ['soft', 'sharp', 'bouncy'],
    ];

    private const RANGES = [
        'stagger' => [0, 1],
        'duration' => [0.1, 3],
        'delay' => [0, 5],
    ];

    public static function normalize(?array $settings): ?array
    {
        if (! filter_var($settings['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return null;
        }

        $motion = self::DEFAULTS;

        foreach (self::CHOICES as $key => $choices) {
            if (in_array($settings[$key] ?? null, $choices, true)) {
                $motion[$key] = $settings[$key];
            }
        }

        if (is_numeric($settings['distance'] ?? null)) {
            $motion['distance'] = (int) min(max((float) $settings['distance'], 0), 400);
        }

        foreach (self::RANGES as $key => [$min, $max]) {
            if (is_numeric($settings[$key] ?? null)) {
                $motion[$key] = (float) min(max((float) $settings[$key], $min), $max);
            }
        }

        $motion['mask'] = filter_var($settings['mask'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $motion;
    }

    public static function attributes(?array $settings): HtmlString
    {
        $motion = self::normalize($settings);

        return new HtmlString($motion === null ? '' : ' data-motion="text" data-text-motion="'.e(json_encode($motion)).'"');
    }
}
