<?php

declare(strict_types=1);

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

namespace App\Tests\Unit\Utils;

use App\Utils\SizeConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class SizeConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-01)
 * @package App\Tests\Unit\Utils
 */
final class SizeConverterTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test SizeConverter: $method
     * @param int $number
     * @param string $method
     * @param int $given
     * @param string $expected
     */
    public function wrapper(int $number, string $method, int $given, string $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [SizeConverter::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(SizeConverter::class));
        $this->assertIsCallable($callback);
        /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
        $this->assertSame($expected, call_user_func($callback, $given));
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
             * Bytes: getHumanReadableSize
             */
            [++$number, 'getHumanReadableSize', 0, '0 Bytes'],
            [++$number, 'getHumanReadableSize', 1, '1 Bytes'],
            [++$number, 'getHumanReadableSize', 200, '200 Bytes'],
            [++$number, 'getHumanReadableSize', 1023, '1023 Bytes'],

            /**
             * kB: getHumanReadableSize
             */
            [++$number, 'getHumanReadableSize', 1 * 1024, '1.00 kB'],
            [++$number, 'getHumanReadableSize', 6 * 1024, '6.00 kB'],
            [++$number, 'getHumanReadableSize', 6 * 1024 + 1000, '6.98 kB'],

            /**
             * MB: getHumanReadableSize
             */
            [++$number, 'getHumanReadableSize', 1 * pow(1024, 2), '1.00 MB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 2), '6.00 MB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 2) + 1000 * 1024, '6.98 MB'],

            /**
             * GB: getHumanReadableSize
             */
            [++$number, 'getHumanReadableSize', 1 * pow(1024, 3), '1.00 GB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 3), '6.00 GB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 3) + 1000 * pow(1024, 2), '6.98 GB'],

            /**
             * TB: getHumanReadableSize
             */
            [++$number, 'getHumanReadableSize', 1 * pow(1024, 4), '1.00 TB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 4), '6.00 TB'],
            [++$number, 'getHumanReadableSize', 6 * pow(1024, 4) + 1000 * pow(1024, 3), '6.98 TB'],
        ];
    }
}
