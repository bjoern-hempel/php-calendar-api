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
                'value' => 1024 ** 4,
            ],
            [
                'unit' => 'GB',
                'value' => 1024 ** 3,
            ],
            [
                'unit' => 'MB',
                'value' => 1024 ** 2,
            ],
            [
                'unit' => 'kB',
                'value' => 1024 ** 1,
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
