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

namespace App\Tests\Unit\Utils\Converter;

use App\Utils\Converter\SizeByte;
use PHPUnit\Framework\TestCase;

/**
 * Class SizeConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link SizeByte
 */
final class SizeByteTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test SizeByte: $method
     * @param int $number
     * @param string $method
     * @param int $given
     * @param string $expected
     */
    public function wrapper(int $number, string $method, int $given, string $expected): void
    {
        /* Arrange */

        /* Act */
        $sizeByte = new SizeByte($given);
        $callback = [$sizeByte, $method];

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertContains($method, get_class_methods(SizeByte::class));
        $this->assertIsCallable($callback);
        $this->assertSame($expected, $sizeByte->{$method}());
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, string|int>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Bytes: getHumanReadable
             */
            [++$number, 'getHumanReadable', 0, '0 Bytes'],
            [++$number, 'getHumanReadable', 1, '1 Bytes'],
            [++$number, 'getHumanReadable', 200, '200 Bytes'],
            [++$number, 'getHumanReadable', 1023, '1023 Bytes'],

            /**
             * kB: getHumanReadable
             */
            [++$number, 'getHumanReadable', 1024, '1.00 kB'],
            [++$number, 'getHumanReadable', 6 * 1024, '6.00 kB'],
            [++$number, 'getHumanReadable', 6 * 1024 + 1000, '6.98 kB'],

            /**
             * MB: getHumanReadable
             */
            [++$number, 'getHumanReadable', 1024 ** 2, '1.00 MB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 2, '6.00 MB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 2 + 1000 * 1024, '6.98 MB'],

            /**
             * GB: getHumanReadable
             */
            [++$number, 'getHumanReadable', 1024 ** 3, '1.00 GB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 3, '6.00 GB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 3 + 1000 * 1024 ** 2, '6.98 GB'],

            /**
             * TB: getHumanReadable
             */
            [++$number, 'getHumanReadable', 1024 ** 4, '1.00 TB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 4, '6.00 TB'],
            [++$number, 'getHumanReadable', 6 * 1024 ** 4 + 1000 * 1024 ** 3, '6.98 TB'],
        ];
    }
}
