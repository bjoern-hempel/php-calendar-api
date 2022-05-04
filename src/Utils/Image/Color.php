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
}
