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

use App\Utils\StringConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class StringConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-07)
 * @package App\Tests\Unit\Utils
 */
final class StringConverterTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test StringConverter: $method
     * @param int $number
     * @param string $method
     * @param string|float|null $param1
     * @param string|float|null $param2
     * @param string|float $expected
     */
    public function wrapper(int $number, string $method, string|float|null $param1, string|float|null $param2, string|float $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [StringConverter::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(StringConverter::class));
        $this->assertIsCallable($callback);

        switch (true) {
            case $param2 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1, $param2));
                break;

            case $param1 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1));
                break;

            default:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback));
        }
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, float|int|string|null>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Test: calculate
             */
            [++$number, 'calculate', '1/10', null, 0.1],
            [++$number, 'calculate', '1/33', 5, 0.0303],

            /**
             * Test: optimizeSlashString
             */
            [++$number, 'optimizeSlashString', '1/10', null, '1/10'],
            [++$number, 'optimizeSlashString', '10/100', null, '1/10'],
            [++$number, 'optimizeSlashString', '100/1000', null, '1/10'],

            /**
             * Test:convertDateTime
             */
            [++$number, 'convertDateTime', '2022:05:01 20:12:35', 'Y-m-d H:i:s', '2022-05-01 20:12:35'],
            [++$number, 'convertDateTime', '2022:05:01 20:12:35', 'c', '2022-05-01T20:12:35+00:00'],
        ];
    }
}
