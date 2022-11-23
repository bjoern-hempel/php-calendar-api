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

namespace App\Utils\Converter;

use App\Tests\Unit\Utils\Converter\SizeByteTest;
use Exception;

/**
 * Class SizeByte
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link SizeByteTest
 */
class SizeByte
{
    protected const CONFIG_HUMAN_READABLE = [
        [
            'unit' => 'TB',
            'value' => 1024 * 1024 * 1024 * 1024,
        ],
        [
            'unit' => 'GB',
            'value' => 1024 * 1024 * 1024,
        ],
        [
            'unit' => 'MB',
            'value' => 1024 * 1024,
        ],
        [
            'unit' => 'kB',
            'value' => 1024,
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

    /**
     * SizeByte constructor.
     *
     * @param int $bytes
     */
    public function __construct(protected int $bytes)
    {
    }

    /**
     * Returns the given bytes into human-readable string.
     *
     * @return string
     * @throws Exception
     */
    public function getHumanReadable(): string
    {
        foreach (self::CONFIG_HUMAN_READABLE as $item) {
            if ($this->bytes >= $item['value']) {
                return match ($item['value']) {
                    0, 1 => sprintf('%d %s', $this->bytes, $item['unit']),
                    default => sprintf('%.2f %s', $this->bytes / $item['value'], $item['unit']),
                };
            }
        }

        throw new Exception(sprintf('The given value must be greater than 0 (%s:%d).', __FILE__, __LINE__));
    }
}
