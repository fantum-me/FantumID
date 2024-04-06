<?php

namespace App\Utils;

class VibrantColorGenerator
{
    public static function getVibrantColor(): array
    {
        [$r, $g, $b] = [0, 0, 0];
        while (self::isColorVibrant($r, $g, $b)) {
            $r = rand(75, 225);
            $g = rand(75, 225);
            $b = rand(75, 225);
        }
        return [$r, $g, $b];
    }

    public static function isColorVibrant(int $r, int $g, int $b): bool
    {
        return abs($r - $g) + abs($g - $b) + abs($r - $b) < 150;
    }
}
