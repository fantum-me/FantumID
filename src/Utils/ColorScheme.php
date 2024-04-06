<?php

namespace App\Utils;

class ColorScheme
{
    const DEFAULT = self::LIGHT;

    const LIGHT = "light";

    const DARK = "dark";

    public static function getValidColorSchemes(): array
    {
        return [self::LIGHT, self::DARK];
    }

    public static function isValid($value): bool
    {
        return in_array($value, self::getValidColorSchemes());
    }
}
