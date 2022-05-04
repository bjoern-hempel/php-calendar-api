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

namespace App\Tests\Unit\Utils\Image;

use App\Utils\Image\Color;
use PHPUnit\Framework\TestCase;

/**
 * Class ColorTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-03)
 * @package App\Tests\Unit\Utils\Image
 */
final class ColorTest extends TestCase
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
     * @param string|int|bool|array{r:int, g:int, b:int} $param1
     * @param string|int|bool|null $param2
     * @param string|int|bool|null $param3
     * @param string|int|bool|null $param4
     * @param string|int|array{r:int, g:int, b:int} $expected
     */
    public function wrapper(int $number, string $method, string|int|bool|array $param1, string|int|bool|null $param2, string|int|bool|null $param3, string|int|bool|null $param4, string|int|array $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [Color::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(Color::class));
        $this->assertIsCallable($callback);

        switch (true) {
            case $param4 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4));
                break;

            case $param3 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3));
                break;

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
     * @return array<int, array<int, mixed>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Test: convertIntegersToInt
             */
            [++$number, 'convertIntegersToInt', 0, 0, 0, null, 0],
            [++$number, 'convertIntegersToInt', 128, 128, 128, null, 128 * 256 * 256 + 128 * 256 + 128],
            [++$number, 'convertIntegersToInt', 255, 255, 255, null, 255 * 256 * 256 + 255 * 256 + 255],
            [++$number, 'convertIntegersToInt', 255, 0, 0, null, 255 * 256 * 256],
            [++$number, 'convertIntegersToInt', 0, 255, 0, null, 255 * 256],
            [++$number, 'convertIntegersToInt', 0, 0, 255, null, 255],

            /**
             * Test: convertIntegersToHex
             */
            [++$number, 'convertIntegersToHex', 0, 0, 0, true, '#000000'],
            [++$number, 'convertIntegersToHex', 128, 128, 128, true, '#808080'],
            [++$number, 'convertIntegersToHex', 255, 255, 255, true, '#FFFFFF'],
            [++$number, 'convertIntegersToHex', 255, 0, 0, true, '#FF0000'],
            [++$number, 'convertIntegersToHex', 0, 255, 0, true, '#00FF00'],
            [++$number, 'convertIntegersToHex', 0, 0, 255, true, '#0000FF'],
            [++$number, 'convertIntegersToHex', 0, 0, 0, false, '000000'],
            [++$number, 'convertIntegersToHex', 128, 128, 128, false, '808080'],
            [++$number, 'convertIntegersToHex', 255, 255, 255, false, 'FFFFFF'],
            [++$number, 'convertIntegersToHex', 255, 0, 0, false, 'FF0000'],
            [++$number, 'convertIntegersToHex', 0, 255, 0, false, '00FF00'],
            [++$number, 'convertIntegersToHex', 0, 0, 255, false, '0000FF'],

            /**
             * Test: convertIntToHex
             */
            [++$number, 'convertIntToHex', Color::convertIntegersToInt(0, 0, 0), true, null, null, '#000000'],
            [++$number, 'convertIntToHex', Color::convertIntegersToInt(128, 128, 128), true, null, null, '#808080'],
            [++$number, 'convertIntToHex', Color::convertIntegersToInt(255, 255, 255), true, null, null, '#FFFFFF'],

            /**
             * Test: convertIntToRgb
             */
            [++$number, 'convertIntToRgb', Color::convertIntegersToInt(0, 0, 0), null, null, null, ['r' => 0, 'g' => 0, 'b' => 0]],
            [++$number, 'convertIntToRgb', Color::convertIntegersToInt(128, 128, 128), null, null, null, ['r' => 128, 'g' => 128, 'b' => 128]],
            [++$number, 'convertIntToRgb', Color::convertIntegersToInt(255, 255, 255), null, null, null, ['r' => 255, 'g' => 255, 'b' => 255]],

            /**
             * Test: convertHexToInt
             */
            [++$number, 'convertHexToInt', '#000000', null, null, null, Color::convertIntegersToInt(0, 0, 0)],
            [++$number, 'convertHexToInt', '#808080', true, null, null, Color::convertIntegersToInt(128, 128, 128)],
            [++$number, 'convertHexToInt', '#FFFFFF', true, null, null, Color::convertIntegersToInt(255, 255, 255)],

            /**
             * Test: convertRgbToInt
             */
            [++$number, 'convertRgbToInt', ['r' => 0, 'g' => 0, 'b' => 0], null, null, null, Color::convertIntegersToInt(0, 0, 0)],
            [++$number, 'convertRgbToInt', ['r' => 128, 'g' => 128, 'b' => 128], true, null, null, Color::convertIntegersToInt(128, 128, 128)],
            [++$number, 'convertRgbToInt', ['r' => 255, 'g' => 255, 'b' => 255], true, null, null, Color::convertIntegersToInt(255, 255, 255)],
        ];
    }
}
