<?php

namespace Database\Seeders\Support;

/**
 * Generates brand-coloured placeholder PNGs in memory.
 *
 * The old site shipped zero images across all six pages, which is on the
 * "do not repeat" list. Seeding real image files rather than leaving the
 * collections empty means Sprint 2 builds its layouts against filled image
 * slots and a working conversion pipeline, instead of discovering both at
 * Sprint 5 when the real photography lands.
 */
class PlaceholderImage
{
    private const BLUE = [0x11, 0x57, 0xB6];

    private const ORANGE = [0xE8, 0x5D, 0x0C];

    private const INK = [0x0A, 0x1A, 0x33];

    private const PAPER = [0xFF, 0xFF, 0xFF];

    /**
     * @return string Raw PNG bytes.
     */
    public static function png(string $label, int $width = 1200, int $height = 800, string $tone = 'blue'): string
    {
        $image = imagecreatetruecolor($width, $height);

        [$r, $g, $b] = match ($tone) {
            'orange' => self::ORANGE,
            'ink' => self::INK,
            'paper' => self::PAPER,
            default => self::BLUE,
        };

        $background = imagecolorallocate($image, $r, $g, $b);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        // A rationed orange rule, matching how the accent is used on the site.
        if ($tone !== 'orange') {
            $rule = imagecolorallocate($image, ...self::ORANGE);
            imagefilledrectangle($image, 0, $height - max(6, (int) ($height * 0.015)), $width, $height, $rule);
        }

        $text = imagecolorallocate($image, ...($tone === 'paper' ? self::INK : self::PAPER));
        $caption = mb_strtoupper(mb_substr($label, 0, 40));

        // Built-in font 5 keeps this dependency free; the real assets replace
        // these files in Sprint 5 anyway.
        $charWidth = imagefontwidth(5);
        $charHeight = imagefontheight(5);
        $x = max(10, (int) (($width - ($charWidth * mb_strlen($caption))) / 2));
        $y = (int) (($height - $charHeight) / 2);

        imagestring($image, 5, $x, $y, $caption, $text);

        ob_start();
        imagepng($image);
        $bytes = (string) ob_get_clean();

        return $bytes;
    }
}
