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

use ArrayIterator;
use Countable;
use Exception;
use GdImage;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Class Palette
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-22)
 * @since 0.1.1 (2022-11-22) Add PHP Magic Number Detector (PHPMND).
 * @version 0.1.0 (2022-05-04) First version.
 * @package App\Utils\Image
 * @implements IteratorAggregate<int, int>
 *
 * @license MIT License
 * @license Copyright (c) 2013 Mathieu Lechat
 * @license Some parts of the code were copied from https://github.com/thephpleague/color-extractor
 * @license The code was ported to PHP 8 and made PHPStan capable.
 * @license https://github.com/thephpleague/color-extractor/blob/master/LICENSE
 */
class Palette implements Countable, IteratorAggregate
{
    final public const COLORS_MAX = 16_777_215;

    /** @var array<int, int> */
    protected array $colors;

    /**
     * Palette constructor.
     */
    protected function __construct()
    {
        $this->colors = [];
    }

    /**
     * Get number of colors.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->colors);
    }

    /**
     * Get iterator of colors.
     *
     * @return ArrayIterator<int, int>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->colors);
    }

    /**
     * Get number of used colors of given color.
     *
     * @param int $color
     * @return int
     */
    public function getColorCount(int $color): int
    {
        return $this->colors[$color];
    }

    /**
     * Returns most used colors.
     *
     * @param int|null $limit = null
     * @return array<int, int>
     */
    public function getMostUsedColors(?int $limit = null): array
    {
        return array_slice($this->colors, 0, $limit, true);
    }

    /**
     * Create GdImage from given filename.
     *
     * @param string $filename
     * @param int|null $backgroundColor
     * @return Palette
     * @throws Exception
     */
    public static function createGdImageFromFilename(string $filename, ?int $backgroundColor = null): Palette
    {
        $imageString = file_get_contents($filename);

        if ($imageString === false) {
            throw new Exception(sprintf('Unable to read image (%s:%d).', __FILE__, __LINE__));
        }

        $image = imagecreatefromstring($imageString);

        if (!$image instanceof GdImage) {
            throw new Exception(sprintf('Unable to create GdImage entity (%s:%d).', __FILE__, __LINE__));
        }

        $palette = self::createPaletteFromGdImage($image, $backgroundColor);

        imagedestroy($image);

        return $palette;
    }

    /**
     * Create Palette from given GdImage.
     *
     * @param GdImage $image
     * @param int|null $backgroundColor
     * @return Palette
     * @throws Exception
     */
    public static function createPaletteFromGdImage(GdImage $image, ?int $backgroundColor = null): Palette
    {
        if ($backgroundColor !== null && (!is_numeric($backgroundColor) || $backgroundColor < 0 || $backgroundColor > self::COLORS_MAX)) {
            throw new InvalidArgumentException(sprintf('"%s" does not represent a valid color', $backgroundColor));
        }

        $palette = new self();

        $areColorsIndexed = !imageistruecolor($image);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $palette->colors = [];

        $backgroundColorRed = ($backgroundColor >> 16) & 0xFF;
        $backgroundColorGreen = ($backgroundColor >> 8) & 0xFF;
        $backgroundColorBlue = $backgroundColor & 0xFF;

        for ($x = 0; $x < $imageWidth; ++$x) {
            for ($y = 0; $y < $imageHeight; ++$y) {
                $color = imagecolorat($image, $x, $y);

                if ($color === false) {
                    throw new Exception(sprintf('Unable to get color (%s:%d).', __FILE__, __LINE__));
                }

                if ($areColorsIndexed) {
                    $colorComponents = imagecolorsforindex($image, $color);
                    $color = ($colorComponents['alpha'] * 16777216) +
                        ($colorComponents['red'] * 65536) +
                        ($colorComponents['green'] * 256) +
                        ($colorComponents['blue']);
                }

                if ($alpha = $color >> 24) {
                    if ($backgroundColor === null) {
                        continue;
                    }

                    $alpha /= 127;
                    $color = (int) (($color >> 16 & 0xFF) * (1 - $alpha) + $backgroundColorRed * $alpha) * 65536 +
                        (int) (($color >> 8 & 0xFF) * (1 - $alpha) + $backgroundColorGreen * $alpha) * 256 +
                        (int) (($color & 0xFF) * (1 - $alpha) + $backgroundColorBlue * $alpha);
                }

                isset($palette->colors[$color]) ?
                    $palette->colors[$color] += 1 :
                    $palette->colors[$color] = 1;
            }
        }

        arsort($palette->colors);

        return $palette;
    }
}
