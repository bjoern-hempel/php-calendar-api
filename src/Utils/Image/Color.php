<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Utils\Image;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * Class Color
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-03)
 * @package App\Utils\Image
 */
class Color
{
    protected const VALUE_HASH = '#';

    /**
     * Converts given rgb integer values to integer.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return int
     */
    #[Pure]
    public static function convertIntegersToInt(int $r, int $g, int $b): int
    {
        return $r * 256 * 256 + $g * 256 + $b;
    }

    /**
     * Converts given rgb integer values to hex value.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @param bool $prependHash = true
     * @return string
     */
    #[Pure]
    public static function convertIntegersToHex(int $r, int $g, int $b, bool $prependHash = true): string
    {
        return self::convertIntToHex($r * 256 * 256 + $g * 256 + $b, $prependHash);
    }

    /**
     * Converts given integer to hex value.
     *
     * Examples:
     * 255 → #0000FF
     * 255*256*256 + 255*256 + 255 → #FFFFFF
     * 128*256*256 + 0*256 + 128 → #800080
     *
     * @param int $color
     * @param bool $prependHash = true
     * @return string
     */
    public static function convertIntToHex(int $color, bool $prependHash = true): string
    {
        return ($prependHash ? self::VALUE_HASH : '').sprintf('%06X', $color);
    }

    /**
     * Converts given integer into rgb array.
     *
     * @param int $color
     * @return array{r:int, g:int, b:int}
     */
    #[ArrayShape(['r' => "int", 'g' => "int", 'b' => "int"])]
    public static function convertIntToRgb(int $color): array
    {
        return [
            'r' => $color >> 16 & 0xFF,
            'g' => $color >> 8 & 0xFF,
            'b' => $color & 0xFF,
        ];
    }

    /**
     * Converts given hex value to integer.
     *
     * Examples:
     * #800080 → 128*256*256 + 0*256 + 128
     *
     * @param string $color
     * @return int
     */
    public static function convertHexToInt(string $color): int
    {
        return intval(hexdec(ltrim($color, self::VALUE_HASH)));
    }

    /**
     * Converts given hex value to integer.
     *
     * Examples:
     * #800080 → 128*256*256 + 0*256 + 128
     *
     * @param string $color
     * @return array{r:int, g:int, b:int}
     */
    #[Pure]
    #[ArrayShape(['r' => "int", 'g' => "int", 'b' => "int"])]
    public static function convertHexToRgb(string $color): array
    {
        return self::convertIntToRgb(self::convertHexToInt($color));
    }

    /**
     * Converts given rgb array into integer.
     *
     * @param array{r:int, g:int, b:int} $rgb
     * @return int
     */
    public static function convertRgbToInt(array $rgb): int
    {
        assert(array_key_exists('r', $rgb));
        assert(array_key_exists('g', $rgb));
        assert(array_key_exists('b', $rgb));
        assert(is_int($rgb['r']));
        assert(is_int($rgb['g']));
        assert(is_int($rgb['b']));

        return ($rgb['r'] * 65536) + ($rgb['g'] * 256) + ($rgb['b']);
    }

    /**
     * Converts given rgb value to srgb.
     *
     * @param int $value
     * @return float
     */
    public static function convertRgbToSrgb(int $value): float
    {
        $value /= 255;

        return $value <= .03928 ?
            $value / 12.92 :
            pow(($value + .055) / 1.055, 2.4);
    }

    /**
     * Converts given xyz value to Lab value.
     *
     * @param float $value
     * @return float
     */
    public static function convertXyzToLab(float $value): float
    {
        return $value > 216 / 24389 ? pow($value, 1 / 3) : 841 * $value / 108 + 4 / 29;
    }

    /**
     * Converts int color to lab array.
     *
     * @param int $color
     * @return array{L: float, a: float, b: float}
     */
    #[ArrayShape(['L' => "float", 'a' => "float", 'b' => "float"])]
    public static function convertIntToLabArray(int $color): array
    {
        return self::convertXyzArrayToLabArray(
            self::convertSrgbArrayToXyzArray(
                self::convertRgbArrayToSrgbArray(
                    self::convertIntToRgb($color)
                )
            )
        );
    }

    /**
     * Converts given rgb array to srgb array.
     *
     * @param array{r: int, g: int, b: int} $rgb
     * @return array{r: float, g: float, b: float}
     */
    #[Pure]
    #[ArrayShape(['r' => "float", 'g' => "float", 'b' => "float"])]
    public static function convertRgbArrayToSrgbArray(array $rgb): array
    {
        return [
            'r' => self::convertRgbToSrgb($rgb['r']),
            'g' => self::convertRgbToSrgb($rgb['g']),
            'b' => self::convertRgbToSrgb($rgb['b']),
        ];
    }

    /**
     * Converts given srgb array into xyz array.
     *
     * @param array{r: float, g: float, b: float} $rgb
     * @return array{x: float, y: float, z: float}
     */
    #[ArrayShape(['x' => "float", 'y' => "float", 'z' => "float"])]
    public static function convertSrgbArrayToXyzArray(array $rgb): array
    {
        return [
            'x' => (.4124564 * $rgb['r']) + (.3575761 * $rgb['g']) + (.1804375 * $rgb['b']),
            'y' => (.2126729 * $rgb['r']) + (.7151522 * $rgb['g']) + (.0721750 * $rgb['b']),
            'z' => (.0193339 * $rgb['r']) + (.1191920 * $rgb['g']) + (.9503041 * $rgb['b']),
        ];
    }

    /**
     * Converts xyz array to lab array.
     *
     * @param array{x: float, y: float, z: float} $xyz
     * @return array{L: float, a: float, b: float}
     */
    #[Pure]
    #[ArrayShape(['L' => "float", 'a' => "float", 'b' => "float"])]
    public static function convertXyzArrayToLabArray(array $xyz): array
    {
        /* http://en.wikipedia.org/wiki/Illuminant_D65#Definition */
        $Xn = .95047;
        $Yn = 1;
        $Zn = 1.08883;

        /* http://en.wikipedia.org/wiki/Lab_color_space#CIELAB-CIEXYZ_conversions */
        return [
            'L' => 116 * self::convertXyzToLab($xyz['y'] / $Yn) - 16,
            'a' => 500 * (self::convertXyzToLab($xyz['x'] / $Xn) - self::convertXyzToLab($xyz['y'] / $Yn)),
            'b' => 200 * (self::convertXyzToLab($xyz['y'] / $Yn) - self::convertXyzToLab($xyz['z'] / $Zn)),
        ];
    }
}
