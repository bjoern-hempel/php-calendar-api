<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2022 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Utils;

use Exception;

/**
 * Class SizeConverter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-01)
 * @package App\Utils
 *
 * @method float|null getAspectRatio()
 * @method int|null getValign()
 */
class SizeConverter
{
    /**
     * Returns the given bytes into human-readable string.
     *
     * @param int $bytes
     * @return string
     * @throws Exception
     */
    public static function getHumanReadableSize(int $bytes): string
    {
        $config = [
            [
                'unit' => 'TB',
                'value' => pow(1024, 4),
            ],
            [
                'unit' => 'GB',
                'value' => pow(1024, 3),
            ],
            [
                'unit' => 'MB',
                'value' => pow(1024, 2),
            ],
            [
                'unit' => 'kB',
                'value' => pow(1024, 1),
            ],
            [
                'unit' => 'Bytes',
                'value' => 1
            ],
            [
                'unit' => 'Bytes',
                'value' => 0
            ],
        ];

        foreach ($config as $item) {
            if ($bytes >= $item['value']) {
                return match ($item['value']) {
                    0, 1 => sprintf('%d %s', $bytes, $item['unit']),
                    default => sprintf('%.2f %s', $bytes / $item['value'], $item['unit']),
                };
            }
        }

        throw new Exception(sprintf('The given value must be greater than 0 (%s:%d).', __FILE__, __LINE__));
    }
}
