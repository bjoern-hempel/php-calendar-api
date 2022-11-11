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
            case 'decimalDegree2GoogleLink':
            case 'decimalDegree2OpenstreetmapLink':
            case 'getDegreeString':
            case 'getDirectionFromPositionsString':
                match (true) {
                    $parameter3 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1, $parameter2, $parameter3)),
                    $parameter2 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1, $parameter2)),
                    $parameter1 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $given2, $parameter1)),
                    // no break
                    default => $this->assertSame($expected, call_user_func($callback, $given1, $given2)),
                };
                break;

            default:
                match (true) {
                    $parameter3 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1, $parameter2, $parameter3)),
                    $parameter2 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1, $parameter2)),
                    $parameter1 !== null => $this->assertSame($expected, call_user_func($callback, $given1, $parameter1)),
                    // no break
                    default => $this->assertSame($expected, call_user_func($callback, $given1)),
                };
        }
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, array<int|string, float|int|string>|float|int|string|null>>
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
            [++$number, 'parseDecimalDegree',  6.932500, null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'decimal' => 6.9325, ]],
            [++$number, 'parseDecimalDegree',  8.015444, null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.5984, 'decimal' => 8.015444, ]],
            [++$number, 'parseDecimalDegree', 46.235194, null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.6984, 'decimal' => 46.235194, ]],
            [++$number, 'parseDecimalDegree', 79.857622, null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'decimal' => 79.857622, ]],

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
            [++$number, 'parseDecimalDegree',  8.015444, null, ['degree' => 8, 'minutes' => 0, 'seconds' => 55.5984, 'decimal' => 8.015444, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ], Coordinate::DIRECTION_EAST],
            [++$number, 'parseDecimalDegree', 79.857622, null, ['degree' => 79, 'minutes' => 51, 'seconds' => 27.4392, 'decimal' => 79.857622, 'type' => Coordinate::TYPE_LONGITUDE, 'direction' => Coordinate::DIRECTION_EAST, ], Coordinate::DIRECTION_EAST],

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
            [++$number, 'parseDecimalDegree',  6.932500, null, ['degree' => 6, 'minutes' => 55, 'seconds' => 57., 'decimal' => 6.932500, 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ], Coordinate::DIRECTION_NORTH],
            [++$number, 'parseDecimalDegree', 46.235194, null, ['degree' => 46, 'minutes' => 14, 'seconds' => 6.6984, 'decimal' => 46.235194, 'type' => Coordinate::TYPE_LATITUDE, 'direction' => Coordinate::DIRECTION_NORTH, ], Coordinate::DIRECTION_NORTH],

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
            [++$number, 'decimalDegree2GoogleLink', 79.857622, 6.932500, 'https://www.google.de/maps/place/79.857622+6.932500'],
            [++$number, 'decimalDegree2GoogleLink', 79.857622, 6.932500, 'https://www.google.de/maps/place/79.857622+6.932500', Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH],

            /**
             * [X, Y], Longitude, Latitude, Decimal Degree → Openstreetmap URL
             */
            [++$number, 'decimalDegree2OpenstreetmapLink', 79.857622, 6.932500, 'https://www.openstreetmap.org/?lat=79.857622&lon=6.932500&mlat=79.857622&mlon=6.932500&zoom=14&layers=M'],
            [++$number, 'decimalDegree2OpenstreetmapLink', 79.857622, 6.932500, 'https://www.openstreetmap.org/?lat=79.857622&lon=6.932500&mlat=79.857622&mlon=6.932500&zoom=14&layers=M', Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_NORTH],

            /**
             * Full location
             */
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635 13.601868', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635, 13.601868', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635,13.601868', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635°,13.601868°', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635° -13.601868°', null, [47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635°, -13.601868°', null, [47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635°,-13.601868°', null, [47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635°,_13.601868°', null, [47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47°54′2.286″E 13°36′6.7248″N', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47°54′2.286″E 13°36′6.7248″N', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47°54′2.286″E, 13°36′6.7248″N', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47° 54′ 2.286″ E, 13° 36′ 6.7248″ N', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', 'E47°54′2.286″ N13°36′6.7248″', null, [47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47°54′2.286″W 13°36′6.7248″N', null, [-47.900635, 13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', 'W47°54′2.286″ S13°36′6.7248″', null, [-47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', 'W 47° 54′ 2.286″ S13° 36′ 6.7248″', null, [-47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', 'W 47° 54′ 2.286″,S 13° 36′ 6.7248″', null, [-47.900635, -13.601868]],
            [++$number, 'parseFullLocation2DecimalDegrees', '47.900635 13°36′6.7248″N', null, [47.900635, 13.601868]],

            /**
             * Degree calculations (Latitude Longitude)
             */
            [++$number, 'getDegreeString', '47.900635 13.601868', '47.900635 13.601868', .0],
            [++$number, 'getDegreeString', '47.900635 13.601868', '48.900635 13.601868', .0], // N: 0.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '48.900635 14.601868', 45.0], // NE: 45.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '47.900635 14.601868', 90.0], // E: 90.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '46.900635 14.601868', 135.0], // SE: 135.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '46.900635 13.601868', 180.0], // S: 180.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '46.900635 12.601868', -135.0], // SW: -135.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '47.900635 12.601868', -90.0], // W: -90.0°
            [++$number, 'getDegreeString', '47.900635 13.601868', '48.900635 12.601868', -45.0], // NW: -45.0°

            /**
             * Degree directions (Latitude Longitude)
             */
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '47.900635 13.601868', 'N'], // N: 0.0° (same position)
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '48.900635 13.601868', 'N'], // N: 0.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '48.900635 14.601868', 'NE'], // NE: 45.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '47.900635 14.601868', 'E'], // E: 90.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '46.900635 14.601868', 'SE'], // SE: 135.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '46.900635 13.601868', 'S'], // S: 180.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '46.900635 12.601868', 'SW'], // SW: -135.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '47.900635 12.601868', 'W'], // W: -90.0°
            [++$number, 'getDirectionFromPositionsString', '47.900635 13.601868', '48.900635 12.601868', 'NW'], // NW: -45.0°
        ];
    }
}
