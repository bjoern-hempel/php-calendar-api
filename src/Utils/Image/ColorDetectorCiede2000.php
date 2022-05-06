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
use SplFixedArray;
use SplPriorityQueue;

/**
 * Class ColorDetectorCiede2000
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-04)
 * @package App\Utils\Image
 *
 * @license MIT License
 * @license Copyright (c) 2013 Mathieu Lechat
 * @license Some parts of the code were copied from https://github.com/thephpleague/color-extractor
 * @license The code was ported to PHP 8 and made PHPStan capable.
 * @license https://github.com/thephpleague/color-extractor/blob/master/LICENSE
 */
class ColorDetectorCiede2000
{
    protected Palette $palette;

    /** @var SplFixedArray<int>|null */
    protected ?SplFixedArray $sortedColors = null;

    /**
     * ColorDetectorCiede2000 constructor.
     */
    public function __construct(Palette $palette)
    {
        $this->palette = $palette;
    }

    /**
     * Extract the most used colors.
     *
     * @param int $colorCount
     * @return array<int>
     * @throws Exception
     */
    public function extract(int $colorCount = 1): array
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        if ($this->sortedColors === null) {
            throw new Exception(sprintf('Got uninitialized color container (%s:%d).', __FILE__, __LINE__));
        }

        return self::mergeColors($this->sortedColors, $colorCount, 100 / $colorCount);
    }

    /**
     * Returns if class is initialized.
     *
     * @return bool
     */
    protected function isInitialized(): bool
    {
        return $this->sortedColors !== null;
    }

    /**
     * Initialize the color container.
     *
     * @return void
     */
    protected function initialize(): void
    {
        $queue = new SplPriorityQueue();

        $this->sortedColors = new SplFixedArray(count($this->palette));

        $i = 0;
        foreach ($this->palette as $color => $count) {
            $labColor = Color::convertIntToLabArray($color);
            $queue->insert(
                $color,
                (sqrt($labColor['a'] * $labColor['a'] + $labColor['b'] * $labColor['b']) ?: 1) *
                (1 - $labColor['L'] / 200) *
                sqrt($count)
            );
            ++$i;
        }

        $i = 0;
        while ($queue->valid()) {
            /** @phpstan-ignore-next-line → intval($queue->current()) is valid to SplFixedArray<int> */
            $this->sortedColors[$i] = intval($queue->current());
            $queue->next();
            ++$i;
        }
    }

    /**
     * Merge given colors.
     *
     * @param SplFixedArray<int> $colors
     * @param int $limit
     * @param float $maxDelta
     * @return array<int, int>
     * @throws Exception
     */
    protected static function mergeColors(SplFixedArray $colors, int $limit, float $maxDelta): array
    {
        $limit = min(count($colors), $limit);

        if ($limit === 1) {
            if ($colors[0] === null) {
                throw new Exception(sprintf('Unexpected color value (%s:%d).', __FILE__, __LINE__));
            }

            return [$colors[0]];
        }

        $labCache = new SplFixedArray($limit - 1);
        $mergedColors = [];

        foreach ($colors as $color) {
            if ($color === null) {
                throw new Exception(sprintf('Unexpected color value (%s:%d).', __FILE__, __LINE__));
            }

            $hasColorBeenMerged = false;

            $colorLab = Color::convertIntToLabArray($color);

            foreach ($mergedColors as $i => $mergedColor) {
                if (self::ciede2000DeltaE($colorLab, $labCache[$i]) < $maxDelta) {
                    $hasColorBeenMerged = true;
                    break;
                }
            }

            if ($hasColorBeenMerged) {
                continue;
            }

            $mergedColorCount = count($mergedColors);
            $mergedColors[] = $color;

            if ($mergedColorCount + 1 == $limit) {
                break;
            }

            $labCache[$mergedColorCount] = $colorLab;
        }

        return $mergedColors;
    }

    /**
     * Converts given lab color arrays into CIEDE2000.
     *
     * @see https://en.wikipedia.org/wiki/Color_difference
     *
     * @param array{L: float, a: float, b: float} $lab1
     * @param array{L: float, a: float, b: float} $lab2
     * @return float
     */
    protected static function ciede2000DeltaE(array $lab1, array $lab2): float
    {
        $C1 = sqrt(pow($lab1['a'], 2) + pow($lab1['b'], 2));
        $C2 = sqrt(pow($lab2['a'], 2) + pow($lab2['b'], 2));
        $Cb = ($C1 + $C2) / 2;

        $G = .5 * (1 - sqrt(pow($Cb, 7) / (pow($Cb, 7) + pow(25, 7))));

        $a1p = (1 + $G) * $lab1['a'];
        $a2p = (1 + $G) * $lab2['a'];

        $C1p = sqrt(pow($a1p, 2) + pow($lab1['b'], 2));
        $C2p = sqrt(pow($a2p, 2) + pow($lab2['b'], 2));

        $h1p = $a1p == 0 && $lab1['b'] == 0 ? 0 : atan2($lab1['b'], $a1p);
        $h2p = $a2p == 0 && $lab2['b'] == 0 ? 0 : atan2($lab2['b'], $a2p);

        $LpDelta = $lab2['L'] - $lab1['L'];
        $CpDelta = $C2p - $C1p;

        if ($C1p * $C2p == 0) {
            $hpDelta = 0;
        } elseif (abs($h2p - $h1p) <= 180) {
            $hpDelta = $h2p - $h1p;
        } elseif ($h2p - $h1p > 180) {
            $hpDelta = $h2p - $h1p - 360;
        } else {
            $hpDelta = $h2p - $h1p + 360;
        }

        $HpDelta = 2 * sqrt($C1p * $C2p) * sin($hpDelta / 2);

        $Lbp = ($lab1['L'] + $lab2['L']) / 2;
        $Cbp = ($C1p + $C2p) / 2;

        if ($C1p * $C2p == 0) {
            $hbp = $h1p + $h2p;
        } elseif (abs($h1p - $h2p) <= 180) {
            $hbp = ($h1p + $h2p) / 2;
        } elseif ($h1p + $h2p < 360) {
            $hbp = ($h1p + $h2p + 360) / 2;
        } else {
            $hbp = ($h1p + $h2p - 360) / 2;
        }

        $T = 1 - .17 * cos($hbp - 30) + .24 * cos(2 * $hbp) + .32 * cos(3 * $hbp + 6) - .2 * cos(4 * $hbp - 63);

        $sigmaDelta = 30 * exp(-pow(($hbp - 275) / 25, 2));

        $Rc = 2 * sqrt(pow($Cbp, 7) / (pow($Cbp, 7) + pow(25, 7)));

        $Sl = 1 + ((.015 * pow($Lbp - 50, 2)) / sqrt(20 + pow($Lbp - 50, 2)));
        $Sc = 1 + .045 * $Cbp;
        $Sh = 1 + .015 * $Cbp * $T;

        $Rt = -sin(2 * $sigmaDelta) * $Rc;

        return sqrt(
            pow($LpDelta / $Sl, 2) +
            pow($CpDelta / $Sc, 2) +
            pow($HpDelta / $Sh, 2) +
            $Rt * ($CpDelta / $Sc) * ($HpDelta / $Sh)
        );
    }
}
