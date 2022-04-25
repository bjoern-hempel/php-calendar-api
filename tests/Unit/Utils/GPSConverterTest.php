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

use App\DataType\Coordinate;
use App\DataType\GPSPosition;
use App\Utils\GPSConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class GPSConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-22)
 * @package App\Tests\Unit\Utils
 */
final class GPSConverterTest extends TestCase
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
     * @param string|float $given1
     * @param string|float|null $given2
     * @param float|array<string, int|float>|string $expected
     * @param string|null $parameter1
     * @param string|null $parameter2
     * @param string|null $parameter3
     */
    public function wrapper(int $number, string $method, string|float $given1, string|float|null $given2, float|array|string $expected, string $parameter1 = null, string $parameter2 = null, string $parameter3 = null): void
    {
        /* Arrange */

        /* Act */
        $callback = [GPSConverter::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(GPSConverter::class));
        $this->assertIsCallable($callback);

        switch ($method) {
            case 'dms2DecimalDegrees':
            case 'decimalDegree2dmss':
            case 'decimalDegree2google':
                match (true) {
                    $parameter3 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1, $parameter2, $parameter3)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    $parameter2 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1, $parameter2)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    $parameter1 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    // no break
                    default => $this->assertSame($expected, call_user_func($callback, $given1, $given2)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                };
                break;

            default:
                match (true) {
                    $parameter3 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1, $parameter2, $parameter3)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    $parameter2 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1, $parameter2)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    $parameter1 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                    // no break
                    default => $this->assertSame($expected, call_user_func($callback, $given1)), /** @phpstan-ignore-line → PHPStan does not detect $callback as valid */
                };
        }
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, array<string, float|int|string>|string|int|float|null>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * X, Longitude, DMS → Array
             */
            [++$number, 'parseDms', '79°51’27.4392"E', null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],
            [++$number, 'parseDms', '79°51\'27.4392"E', null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],
            [++$number, 'parseDms', '79° 51\' 27.4392\'’ E', null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],
            [++$number, 'parseDms', '79° 51\' 27.4392" E', null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],
            [++$number, 'parseDms', '008°00′55.60″E', null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.6, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],
            [++$number, 'parseDms', 'E008°00′55.60″', null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.6, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ]],

            /**
             * Y, Latitude, DMS → Array
             */
            [++$number, 'parseDms', '6°55’57"N', null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],
            [++$number, 'parseDms', '6°55\'57"N', null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],
            [++$number, 'parseDms', '6° 55\' 57\'’ N', null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],
            [++$number, 'parseDms', '6° 55\' 57" N', null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],
            [++$number, 'parseDms', '46°14′06.70″N', null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.70, 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],
            [++$number, 'parseDms', 'N46°14′06.70″', null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.70, 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ]],

            /**
             * X, Longitude, DMS → Decimal Degree
             */
            [++$number, 'dms2DecimalDegree', '79°51’27.4392"E', null, 79.857622],
            [++$number, 'dms2DecimalDegree', '79°51\'27.4392"E', null, 79.857622],
            [++$number, 'dms2DecimalDegree', '79° 51\' 27.4392\'’ E', null, 79.857622],
            [++$number, 'dms2DecimalDegree', '79° 51\' 27.4392" E', null, 79.857622],
            [++$number, 'dms2DecimalDegree', '008°00′55.60″E', null, 8.015444],
            [++$number, 'dms2DecimalDegree', 'E008°00′55.60″', null, 8.015444],

            /**
             * Y, Latitude, DMS → Decimal Degree
             */
            [++$number, 'dms2DecimalDegree', '6°55’57"N', null, 6.932500],
            [++$number, 'dms2DecimalDegree', '6°55\'57"N', null, 6.932500],
            [++$number, 'dms2DecimalDegree', '6° 55\' 57\'’ N', null, 6.932500],
            [++$number, 'dms2DecimalDegree', '6° 55\' 57" N', null, 6.932500],
            [++$number, 'dms2DecimalDegree', '46°14′06.70″N', null, 46.235194],
            [++$number, 'dms2DecimalDegree', 'N46°14′06.70″', null, 46.235194],

            /**
             * Latitude/Longitude, Decimal Degree → Array
             */
            [++$number, 'parseDecimalDegree',  6.932500, null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., ]],
            [++$number, 'parseDecimalDegree',  8.015444, null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.5984, ]],
            [++$number, 'parseDecimalDegree', 46.235194, null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.6984, ]],
            [++$number, 'parseDecimalDegree', 79.857622, null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, ]],

            /**
             * Latitude/Longitude, Decimal Degree → String
             */
            [++$number, 'decimalDegree2dms',   6.932500, null, '6°55′57″'],
            [++$number, 'decimalDegree2dms',   8.015444, null, '8°0′55.5984″'],
            [++$number, 'decimalDegree2dms',  46.235194, null, '46°14′6.6984″'],
            [++$number, 'decimalDegree2dms',  79.857622, null, '79°51′27.4392″'],

            /**
             * X, Longitude, Decimal Degree → Array
             */
            [++$number, 'parseDecimalDegree',  8.015444, null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.5984, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ], Coordinate::DIRECTION_EAST],
            [++$number, 'parseDecimalDegree', 79.857622, null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ], Coordinate::DIRECTION_EAST],

            /**
             * X, Longitude, Decimal Degree → String
             */
            [++$number, 'decimalDegree2dms',   8.015444, null, '8°0′55.5984″E', Coordinate::DIRECTION_EAST],
            [++$number, 'decimalDegree2dms',  79.857622, null, '79°51′27.4392″E', Coordinate::DIRECTION_EAST],
            [++$number, 'decimalDegree2dms',   8.015444, null, '8°0′55.5984″E', Coordinate::DIRECTION_EAST, GPSPosition::FORMAT_DMS_SHORT_1],
            [++$number, 'decimalDegree2dms',  79.857622, null, '79°51′27.4392″E', Coordinate::DIRECTION_EAST, GPSPosition::FORMAT_DMS_SHORT_1],
            [++$number, 'decimalDegree2dms',   8.015444, null, 'E8°0′55.5984″', Coordinate::DIRECTION_EAST, GPSPosition::FORMAT_DMS_SHORT_2],
            [++$number, 'decimalDegree2dms',  79.857622, null, 'E79°51′27.4392″', Coordinate::DIRECTION_EAST, GPSPosition::FORMAT_DMS_SHORT_2],

            /**
             * Y, Latitude, Decimal Degree → Array
             */
            [++$number, 'parseDecimalDegree',  6.932500, null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ], Coordinate::DIRECTION_NORTH],
            [++$number, 'parseDecimalDegree', 46.235194, null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.6984, 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ], Coordinate::DIRECTION_NORTH],

            /**
             * Y, Latitude, Decimal Degree → String
             */
            [++$number, 'decimalDegree2dms',   6.932500, null, '6°55′57″N', Coordinate::DIRECTION_NORTH],
            [++$number, 'decimalDegree2dms',  46.235194, null, '46°14′6.6984″N', Coordinate::DIRECTION_NORTH],
            [++$number, 'decimalDegree2dms',   6.932500, null, '6°55′57″N', Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_1],
            [++$number, 'decimalDegree2dms',  46.235194, null, '46°14′6.6984″N', Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_1],
            [++$number, 'decimalDegree2dms',   6.932500, null, 'N6°55′57″', Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_2],
            [++$number, 'decimalDegree2dms',  46.235194, null, 'N46°14′6.6984″', Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_2],

            /**
             * [X, Y], Longitude, Latitude, DMS → Decimal Degree
             */
            [++$number, 'dms2DecimalDegrees', '79°51’27.4392"E', '6°55’57"N',  ['longitude' => 79.857622, 'latitude' => 6.932500]],
            [++$number, 'dms2DecimalDegrees', '79°51\'27.4392"E', '6°55\'57"N',  ['longitude' => 79.857622, 'latitude' => 6.932500]],
            [++$number, 'dms2DecimalDegrees', '79° 51\' 27.4392\'’ E', '6° 55\' 57\'’ N', ['longitude' => 79.857622, 'latitude' => 6.932500]],
            [++$number, 'dms2DecimalDegrees', '79° 51\' 27.4392" E', '6° 55\' 57" N',  ['longitude' => 79.857622, 'latitude' => 6.932500]],
            [++$number, 'dms2DecimalDegrees', '008°00′55.60″E', '46°14′06.70″N', ['longitude' => 8.015444, 'latitude' => 46.235194]],
            [++$number, 'dms2DecimalDegrees', 'E008°00′55.60″', 'N46°14′06.70″', ['longitude' => 8.015444, 'latitude' => 46.235194]],

            /**
             * [X, Y], Longitude, Latitude, Decimal Degree → String
             */
            [++$number, 'decimalDegree2dmss', 79.857622, 6.932500, ['longitude' => '79°51′27.4392″', 'latitude' => '6°55′57″']],
            [++$number, 'decimalDegree2dmss', 79.857622, 6.932500, ['longitude' => '79°51′27.4392″E', 'latitude' => '6°55′57″N'], Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH],
            [++$number, 'decimalDegree2dmss', 79.857622, 6.932500, ['longitude' => '79°51′27.4392″E', 'latitude' => '6°55′57″N'], Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_1],
            [++$number, 'decimalDegree2dmss', 79.857622, 6.932500, ['longitude' => 'E79°51′27.4392″', 'latitude' => 'N6°55′57″'], Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH, GPSPosition::FORMAT_DMS_SHORT_2],

            /**
             * [X, Y], Longitude, Latitude, Decimal Degree → Google URL
             */
            [++$number, 'decimalDegree2google', 79.857622, 6.932500, 'https://www.google.de/maps/place/79°51′27.4392″+6°55′57″'],
            [++$number, 'decimalDegree2google', 79.857622, 6.932500, 'https://www.google.de/maps/place/79°51′27.4392″E+6°55′57″N', Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH],
        ];
    }
}
