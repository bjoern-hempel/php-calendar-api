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
     * @param string|int|float|bool|array{r:int, g:int, b:int} $param1
     * @param string|int|bool|null $param2
     * @param string|int|bool|null $param3
     * @param string|int|bool|null $param4
     * @param string|int|bool|null $param5
     * @param string|int|float|array{r:int, g:int, b:int} $expected
     */
    public function wrapper(int $number, string $method, string|int|float|bool|array $param1, string|int|bool|null $param2, string|int|bool|null $param3, string|int|bool|null $param4, string|int|bool|null $param5, string|int|float|array $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [Color::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(Color::class));
        $this->assertIsCallable($callback);

        match (true) {
            $param5 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4, $param5)),
            $param4 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4)),
            $param3 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3)),
            $param2 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2)),
            $param1 !== null => $this->assertSame($expected, call_user_func($callback, $param1)),
        };
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
             * Test: convertRgbToHex (single color value)
             */
            [++$number, 'convertRgbToHex', 255, null, null, null, null, 'FF'],
            [++$number, 'convertRgbToHex', 0, null, null, null, null, '00'],
            [++$number, 'convertRgbToHex', 128, 6, null, null, null, '80'],
            [++$number, 'convertRgbToHex', 255, true, null, null, null, 'ff'],
            [++$number, 'convertRgbToHex', 0, true, null, null, null, '00'],
            [++$number, 'convertRgbToHex', 128, true, null, null, null, '80'],

            /**
             * Test: convertRgbToSrgb (single color value)
             */
            [++$number, 'convertRgbToSrgb', 255, null, null, null, null, 1.],
            [++$number, 'convertRgbToSrgb', 0, null, null, null, null, 0.],
            [++$number, 'convertRgbToSrgb', 128, 6, null, null, null, 0.215861],



            /**
             * Test: convertXyzToLab (single color value)
             */
            [++$number, 'convertXyzToLab', 1., 6, null, null, null, 1.],
            [++$number, 'convertXyzToLab', 0., 6, null, null, null, 0.137931],
            [++$number, 'convertXyzToLab', 0.5, 6, null, null, null, 0.793701],



            /**
             * Test: convertRgbsToInt (full color value)
             */
            [++$number, 'convertRgbsToInt', 0, 0, 0, null, null, 0],
            [++$number, 'convertRgbsToInt', 128, 128, 128, null, null, 128 * 256 * 256 + 128 * 256 + 128],
            [++$number, 'convertRgbsToInt', 255, 255, 255, null, null, 255 * 256 * 256 + 255 * 256 + 255],
            [++$number, 'convertRgbsToInt', 255, 0, 0, null, null, 255 * 256 * 256],
            [++$number, 'convertRgbsToInt', 0, 255, 0, null, null, 255 * 256],
            [++$number, 'convertRgbsToInt', 0, 0, 255, null, null, 255],

            /**
             * Test: convertRgbsToHex (full color value)
             */
            [++$number, 'convertRgbsToHex', 0, 0, 0, true, null, '#000000'],
            [++$number, 'convertRgbsToHex', 128, 128, 128, true, null, '#808080'],
            [++$number, 'convertRgbsToHex', 255, 255, 255, true, null, '#FFFFFF'],
            [++$number, 'convertRgbsToHex', 255, 0, 0, true, null, '#FF0000'],
            [++$number, 'convertRgbsToHex', 0, 255, 0, true, null, '#00FF00'],
            [++$number, 'convertRgbsToHex', 0, 0, 255, true, null, '#0000FF'],
            [++$number, 'convertRgbsToHex', 0, 0, 0, false, null, '000000'],
            [++$number, 'convertRgbsToHex', 128, 128, 128, false, null, '808080'],
            [++$number, 'convertRgbsToHex', 255, 255, 255, false, null, 'FFFFFF'],
            [++$number, 'convertRgbsToHex', 255, 0, 0, false, null, 'FF0000'],
            [++$number, 'convertRgbsToHex', 0, 255, 0, false, null, '00FF00'],
            [++$number, 'convertRgbsToHex', 0, 0, 255, false, null, '0000FF'],
            [++$number, 'convertRgbsToHex', 0, 0, 0, true, true, '#000000'],
            [++$number, 'convertRgbsToHex', 128, 128, 128, true, true, '#808080'],
            [++$number, 'convertRgbsToHex', 255, 255, 255, true, true, '#ffffff'],
            [++$number, 'convertRgbsToHex', 255, 0, 0, true, true, '#ff0000'],
            [++$number, 'convertRgbsToHex', 0, 255, 0, true, true, '#00ff00'],
            [++$number, 'convertRgbsToHex', 0, 0, 255, true, true, '#0000ff'],



            /**
             * Test: convertIntToHex
             */
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(0, 0, 0), null, null, null, null, '#000000'],
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(128, 128, 128), null, null, null, null, '#808080'],
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(255, 255, 255), null, null, null, null, '#FFFFFF'],
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(255, 255, 255), false, null, null, null, 'FFFFFF'],
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(255, 255, 255), true, true, null, null, '#ffffff'],
            [++$number, 'convertIntToHex', Color::convertRgbsToInt(255, 255, 255), false, true, null, null, 'ffffff'],

            /**
             * Test: convertIntToRgbArray
             */
            [++$number, 'convertIntToRgbArray', Color::convertRgbsToInt(0, 0, 0), null, null, null, null, [Color::COLOR_INDEX_RGB_RED => 0, Color::COLOR_INDEX_RGB_GREEN => 0, Color::COLOR_INDEX_RGB_BLUE => 0]],
            [++$number, 'convertIntToRgbArray', Color::convertRgbsToInt(128, 128, 128), null, null, null, null, [Color::COLOR_INDEX_RGB_RED => 128, Color::COLOR_INDEX_RGB_GREEN => 128, Color::COLOR_INDEX_RGB_BLUE => 128]],
            [++$number, 'convertIntToRgbArray', Color::convertRgbsToInt(255, 255, 255), null, null, null, null, [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255]],

            /**
             * Test: convertIntToLabArray
             */
            [++$number, 'convertIntToLabArray', Color::convertRgbsToInt(0, 0, 0), 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 0., Color::COLOR_INDEX_LAB_A => 0., Color::COLOR_INDEX_LAB_B => 0.]],
            [++$number, 'convertIntToLabArray', Color::convertRgbsToInt(128, 128, 128), 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 53.585016, Color::COLOR_INDEX_LAB_A => -1.E-5, Color::COLOR_INDEX_LAB_B => 4.E-6]],
            [++$number, 'convertIntToLabArray', Color::convertRgbsToInt(255, 255, 255), 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 100., Color::COLOR_INDEX_LAB_A => -1.7E-5, Color::COLOR_INDEX_LAB_B => 7.E-6]],



            /**
             * Test: convertHexToInt
             */
            [++$number, 'convertHexToInt', '#000000', null, null, null, null, Color::convertRgbsToInt(0, 0, 0)],
            [++$number, 'convertHexToInt', '#808080', true, null, null, null, Color::convertRgbsToInt(128, 128, 128)],
            [++$number, 'convertHexToInt', '#FFFFFF', true, null, null, null, Color::convertRgbsToInt(255, 255, 255)],

            /**
             * Test: convertHexToRgbArray
             */
            [++$number, 'convertHexToRgbArray', '#000000', null, null, null, null, [Color::COLOR_INDEX_RGB_RED => 0, Color::COLOR_INDEX_RGB_GREEN => 0, Color::COLOR_INDEX_RGB_BLUE => 0]],
            [++$number, 'convertHexToRgbArray', '#808080', true, null, null, null, [Color::COLOR_INDEX_RGB_RED => 128, Color::COLOR_INDEX_RGB_GREEN => 128, Color::COLOR_INDEX_RGB_BLUE => 128]],
            [++$number, 'convertHexToRgbArray', '#FFFFFF', true, null, null, null, [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255]],



            /**
             * Test: convertRgbArrayToInt
             */
            [++$number, 'convertRgbArrayToInt', [Color::COLOR_INDEX_RGB_RED => 0, Color::COLOR_INDEX_RGB_GREEN => 0, Color::COLOR_INDEX_RGB_BLUE => 0], null, null, null, null, Color::convertRgbsToInt(0, 0, 0)],
            [++$number, 'convertRgbArrayToInt', [Color::COLOR_INDEX_RGB_RED => 128, Color::COLOR_INDEX_RGB_GREEN => 128, Color::COLOR_INDEX_RGB_BLUE => 128], null, null, null, null, Color::convertRgbsToInt(128, 128, 128)],
            [++$number, 'convertRgbArrayToInt', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], null, null, null, null, Color::convertRgbsToInt(255, 255, 255)],

            /**
             * Test: convertRgbArrayToHex
             */
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 0, Color::COLOR_INDEX_RGB_GREEN => 0, Color::COLOR_INDEX_RGB_BLUE => 0], null, null, null, null, '#000000'],
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 128, Color::COLOR_INDEX_RGB_GREEN => 128, Color::COLOR_INDEX_RGB_BLUE => 128], null, null, null, null, '#808080'],
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], null, null, null, null, '#FFFFFF'],
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], false, null, null, null, 'FFFFFF'],
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], true, true, null, null, '#ffffff'],
            [++$number, 'convertRgbArrayToHex', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], false, true, null, null, 'ffffff'],

            /**
             * Test: convertRgbArrayToSrgbArray
             */
            [++$number, 'convertRgbArrayToSrgbArray', [Color::COLOR_INDEX_RGB_RED => 0, Color::COLOR_INDEX_RGB_GREEN => 0, Color::COLOR_INDEX_RGB_BLUE => 0], 6, null, null, null, [Color::COLOR_INDEX_SRGB_RED => 0., Color::COLOR_INDEX_SRGB_GREEN => 0., Color::COLOR_INDEX_SRGB_BLUE => 0.]],
            [++$number, 'convertRgbArrayToSrgbArray', [Color::COLOR_INDEX_RGB_RED => 128, Color::COLOR_INDEX_RGB_GREEN => 128, Color::COLOR_INDEX_RGB_BLUE => 128], 6, null, null, null, [Color::COLOR_INDEX_SRGB_RED => 0.215861, Color::COLOR_INDEX_SRGB_GREEN => 0.215861, Color::COLOR_INDEX_SRGB_BLUE => 0.215861]],
            [++$number, 'convertRgbArrayToSrgbArray', [Color::COLOR_INDEX_RGB_RED => 255, Color::COLOR_INDEX_RGB_GREEN => 255, Color::COLOR_INDEX_RGB_BLUE => 255], 6, null, null, null, [Color::COLOR_INDEX_SRGB_RED => 1., Color::COLOR_INDEX_SRGB_GREEN => 1., Color::COLOR_INDEX_SRGB_BLUE => 1.]],



            /**
             * Test: convertSrgbArrayToXyzArray
             */
            [++$number, 'convertSrgbArrayToXyzArray', [Color::COLOR_INDEX_SRGB_RED => 0., Color::COLOR_INDEX_SRGB_GREEN => 0., Color::COLOR_INDEX_SRGB_BLUE => 0.], 6, null, null, null, [Color::COLOR_INDEX_XYZ_X => 0., Color::COLOR_INDEX_XYZ_Y => 0., Color::COLOR_INDEX_XYZ_Z => 0.]],
            [++$number, 'convertSrgbArrayToXyzArray', [Color::COLOR_INDEX_SRGB_RED => 0.215861, Color::COLOR_INDEX_SRGB_GREEN => 0.215861, Color::COLOR_INDEX_SRGB_BLUE => 0.215861], 6, null, null, null, [Color::COLOR_INDEX_XYZ_X => 0.205169, Color::COLOR_INDEX_XYZ_Y => 0.215861, Color::COLOR_INDEX_XYZ_Z => 0.235036]],
            [++$number, 'convertSrgbArrayToXyzArray', [Color::COLOR_INDEX_SRGB_RED => 1., Color::COLOR_INDEX_SRGB_GREEN => 1., Color::COLOR_INDEX_SRGB_BLUE => 1.], 6, null, null, null, [Color::COLOR_INDEX_XYZ_X => 0.95047, Color::COLOR_INDEX_XYZ_Y => 1., Color::COLOR_INDEX_XYZ_Z => 1.08883]],



            /**
             * Test: convertXyzArrayToLabArray
             */
            [++$number, 'convertXyzArrayToLabArray', [Color::COLOR_INDEX_XYZ_X => 0., Color::COLOR_INDEX_XYZ_Y => 0., Color::COLOR_INDEX_XYZ_Z => 0.], 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 0., Color::COLOR_INDEX_LAB_A => 0., Color::COLOR_INDEX_LAB_B => 0.]],
            [++$number, 'convertXyzArrayToLabArray', [Color::COLOR_INDEX_XYZ_X => 0.205169, Color::COLOR_INDEX_XYZ_Y => 0.215861, Color::COLOR_INDEX_XYZ_Z => 0.235036], 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 53.585067, Color::COLOR_INDEX_LAB_A => -0.000197, Color::COLOR_INDEX_LAB_B => -1.1E-5]],
            [++$number, 'convertXyzArrayToLabArray', [Color::COLOR_INDEX_XYZ_X => 0.95047, Color::COLOR_INDEX_XYZ_Y => 1., Color::COLOR_INDEX_XYZ_Z => 1.08883], 6, null, null, null, [Color::COLOR_INDEX_LAB_LIGHTNESS => 100., Color::COLOR_INDEX_LAB_A => 0., Color::COLOR_INDEX_LAB_B => 0.]],
        ];
    }
}
