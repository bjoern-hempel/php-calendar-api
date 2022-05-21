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

use Exception;
use GdImage;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ColorDetectorSimple
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-05)
 * @package App\Utils\Image
 */
class ColorDetectorSimple
{
    protected GdImage $gdImage;

    public const DEFAULT_REDUCE_BRIGHTNESS = false;

    public const DEFAULT_REDUCE_GRADIENTS = false;

    public const DEFAULT_DELTA = 24;

    /**
     * ColorDetectorSimple constructor.
     *
     * @param GdImage $gdImage
     */
    public function __construct(GdImage $gdImage)
    {
        $this->gdImage = $gdImage;
    }

    /**
     * Reduces the given color information (single color).
     *
     * @param int $color
     * @param int $delta
     * @return int
     */
    protected function reduceColorInformation(int $color, int $delta): int
    {
        if ($delta <= 1) {
            return $color;
        }

        $halfDelta = $delta > 2 ? $delta / 2 - 1 : 0;

        $color = intval((($color) + $halfDelta) / $delta) * $delta;

        if ($color >= 256) {
            $color = 255;
        }

        return $color;
    }

    /**
     * Reduces the given color information (array).
     *
     * @param array{red: int, green: int, blue: int} $colors
     * @param int $delta
     * @return array{r: int, g: int, b: int}
     */
    #[ArrayShape(['r' => "int", 'g' => "int", 'b' => "int"])]
    protected function reduceColorArrayInformation(array $colors, int $delta): array
    {
        return [
            'r' => $this->reduceColorInformation($colors['red'], $delta),
            'g' => $this->reduceColorInformation($colors['green'], $delta),
            'b' => $this->reduceColorInformation($colors['blue'], $delta),
        ];
    }

    /**
     * Sorts color array.
     *
     * @param array<string, int> $colorArray
     * @return void
     */
    protected function sortColorArray(array &$colorArray): void
    {
        arsort($colorArray, SORT_NUMERIC);
    }

    /**
     * Reduces the gradient of given color array.
     *
     * @param array<string, int> $colorArray
     * @param int $delta
     * @param bool $reduceGradients
     * @return void
     */
    protected function reduceGradients(array &$colorArray, int $delta, bool $reduceGradients = true): void
    {
        if (!$reduceGradients) {
            return;
        }

        $this->sortColorArray($colorArray);

        /** @var array<string, string> $gradients */
        $gradients = [];
        foreach ($colorArray as $colorHex => $num) {
            if (! isset($gradients[$colorHex])) {
                $colorHexNew = $this->findAdjacent($colorHex, $gradients, $delta);
                $gradients[$colorHex] = $colorHexNew;
            } else {
                $colorHexNew = $gradients[$colorHex];
            }

            if ($colorHex != $colorHexNew) {
                $colorArray[$colorHex] = 0;
                $colorArray[$colorHexNew] += $num;
            }
        }
    }

    /**
     * Reduces the brightness of given color array.
     *
     * @param array<string, int> $colorArray
     * @param int $delta
     * @param bool $reduceBrightness
     * @return void
     */
    protected function reduceBrightness(array &$colorArray, int $delta, bool $reduceBrightness = true): void
    {
        if (!$reduceBrightness) {
            return;
        }

        $this->sortColorArray($colorArray);

        /** @var array<string, int> $brightness */
        $brightness = [];
        foreach ($colorArray as $colorHex => $num) {
            if (!isset($brightness[$colorHex])) {
                $colorHexNew = $this->normalize($colorHex, $brightness, $delta);
                $brightness[$colorHex] = $colorHexNew;
            } else {
                $colorHexNew = $brightness[$colorHex];
            }

            if ($colorHex != $colorHexNew) {
                $colorArray[$colorHex] = 0;
                $colorArray[$colorHexNew] += $num;
            }
        }
    }

    /**
     * Normalize given hex color.
     *
     * @param string|int $colorHex
     * @param array<string|int, string|int> $colorArray
     * @param int $delta
     * @return string
     */
    public function normalize(string|int $colorHex, array $colorArray, int $delta): string
    {
        if (is_int($colorHex)) {
            $colorHex = strval($colorHex);
        }

        $lowest = 255;
        $highest = 0;

        $colors = [
            'r' => intval(hexdec(substr($colorHex, 0, 2))),
            'g' => intval(hexdec(substr($colorHex, 2, 2))),
            'b' => intval(hexdec(substr($colorHex, 4, 2))),
        ];

        if ($colors['r'] < $lowest) {
            $lowest = $colors['r'];
        }
        if ($colors['g'] < $lowest) {
            $lowest = $colors['g'];
        }
        if ($colors['b'] < $lowest) {
            $lowest = $colors['b'];
        }

        if ($colors['r'] > $highest) {
            $highest = $colors['r'];
        }
        if ($colors['g'] > $highest) {
            $highest = $colors['g'];
        }
        if ($colors['b'] > $highest) {
            $highest = $colors['b'];
        }

        if ($lowest === $highest) {
            if ($delta <= 32) {
                if ($highest >= (255 - $delta)) {
                    return $colorHex;
                }
            } else {
                return $colorHex;
            }
        }

        for (; $highest < 256; $lowest += $delta, $highest += $delta) {
            $colorHexNew = Color::convertRgbArrayToHex($colors, false, true);

            if (isset($colorArray[$colorHexNew])) {
                return $colorHexNew;
            }
        }

        return $colorHex;
    }

    /**
     * Finds adjacent.
     *
     * @param string|int $colorHex
     * @param array<string|int, string> $gradients
     * @param int $delta
     * @return string
     */
    public function findAdjacent(string|int $colorHex, array $gradients, int $delta): string
    {
        if (is_int($colorHex)) {
            $colorHex = strval($colorHex);
        }

        $red = intval(hexdec(substr(strval($colorHex), 0, 2)));
        $green  = intval(hexdec(substr(strval($colorHex), 2, 2)));
        $blue = intval(hexdec(substr(strval($colorHex), 4, 2)));

        if ($red > $delta) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red - $delta, 'g' => $green, 'b' => $blue], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }
        if ($green > $delta) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red, 'g' => $green - $delta, 'b' => $blue], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }
        if ($blue > $delta) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red, 'g' => $green, 'b' => $blue - $delta], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }

        if ($red < (255 - $delta)) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red + $delta, 'g' => $green, 'b' => $blue], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }
        if ($green < (255 - $delta)) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red, 'g' => $green + $delta, 'b' => $blue], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }
        if ($blue < (255 - $delta)) {
            $colorHexNew = Color::convertRgbArrayToHex(['r' => $red, 'g' => $green, 'b' => $blue + $delta], false, true);
            if (isset($gradients[$colorHexNew])) {
                return $gradients[$colorHexNew];
            }
        }

        return $colorHex;
    }

    /**
     * Gets the color array from $this->gdImage.
     *
     * @param int $delta
     * @param bool $reduceBrightness
     * @param bool $reduceGradients
     * @return array<string, int>
     * @throws Exception
     */
    protected function getColorArray(int $delta, bool $reduceBrightness = true, bool $reduceGradients = true): array
    {
        /* Get image properties. */
        $imageWidth = imagesx($this->gdImage);
        $imageHeight = imagesy($this->gdImage);

        $totalPixelCount = 0;

        $colorArray = [];

        /* Iterate through the image. */
        for ($y=0; $y < $imageHeight; $y++) {
            for ($x=0; $x < $imageWidth; $x++) {
                $totalPixelCount++;

                /* Get the index of the color of a pixel. */
                $index = imagecolorat($this->gdImage, $x, $y);

                if ($index === false) {
                    throw new Exception(sprintf('Unable to get color of pixel (%s:%d).', __FILE__, __LINE__));
                }

                /** @var array{red: int, green: int, blue: int} $color */
                $color = imagecolorsforindex($this->gdImage, $index);

                /* Get the colors for an index. */
                $colorReduced = $this->reduceColorArrayInformation($color, $delta);

                $colorHex = Color::convertRgbArrayToHex($colorReduced, false, true);

                if (! isset($colorArray[$colorHex])) {
                    $colorArray[$colorHex] = 1;
                } else {
                    $colorArray[$colorHex]++;
                }
            }
        }

        $this->reduceGradients($colorArray, $delta, $reduceGradients);

        $this->reduceBrightness($colorArray, $delta, $reduceBrightness);

        $this->sortColorArray($colorArray);

        /* convert counts to percentages */
        foreach ($colorArray as $key => $value) {
            $colorArray[$key] = (float)$value / $totalPixelCount;
        }

        return $colorArray;
    }

    /**
     * Returns the colors of the image in an array.
     *
     * @param int $count
     * @param bool $reduceBrightness
     * @param bool $reduceGradients
     * @param int $delta
     * @return array<string, float>
     * @throws Exception
     */
    public function getColors(int $count = 20, bool $reduceBrightness = self::DEFAULT_REDUCE_BRIGHTNESS, bool $reduceGradients = self::DEFAULT_REDUCE_GRADIENTS, int $delta = self::DEFAULT_DELTA): array
    {
        $colorArray = $this->getColorArray($delta, $reduceBrightness, $reduceGradients);

        return $count <= 0 ? $colorArray : array_slice($colorArray, 0, $count, true);
    }
}