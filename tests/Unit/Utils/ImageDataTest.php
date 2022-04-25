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

use App\Utils\ImageData;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageDataTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-25)
 * @package App\Tests\Unit\Utils
 */
final class ImageDataTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test SizeConverter: $method
     * @param int $number
     * @param string $imagePath
     * @param array<int, array<int, array<string, array<string, float|int|string|null>>|int|string>> $expected
     * @throws Exception
     */
    public function wrapper(int $number, string $imagePath, array $expected): void
    {
        /* Arrange */
        $imageData = new ImageData($imagePath);

        /* Act */
        $current = $imageData->getDataImage();

        /* Assert */
        $this->assertSame($expected, $current);
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, array<string, array<string, float|int|string|null>>|int|string>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Image, With GPS
             */
            [++$number, 'data/tests/images/properties/img-with-gps.jpg', [
                'device-manufacturer' => [
                    'title' => 'Device Manufacturer',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'OnePlus',
                    'value-formatted' => 'OnePlus ',
                ],
                'device-model' => [
                    'title' => 'Device Model',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'IN2023',
                    'value-formatted' => 'IN2023 ',
                ],
                'exif-version' => [
                    'title' => 'Exif Version',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '0220',
                    'value-formatted' => '0220 ',
                ],
                'gps-google-link' => [
                    'title' => 'GPS Google',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'https://www.google.de/maps/place/14°25′1.92″E+50°3′46.1484″N',
                    'value-formatted' => 'https://www.google.de/maps/place/14°25′1.92″E+50°3′46.1484″N ',
                ],
                'gps-height' => [
                    'title' => 'GPS Height',
                    'format' => '%.2f',
                    'unit' => ' m',
                    'unit-before' => null,
                    'value' => 278.471,
                    'value-formatted' => '278.471  m',
                ],
                'gps-latitude-decimal-degree' => [
                    'title' => 'GPS Latitude Decimal Degree',
                    'format' => '%s',
                    'unit' => '°',
                    'unit-before' => null,
                    'value' => 50.062819,
                    'value-formatted' => '50.062819 °',
                ],
                'gps-latitude-direction' => [
                    'title' => 'GPS Latitude Direction',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'N',
                    'value-formatted' => 'N ',
                ],
                'gps-latitude-dms' => [
                    'title' => 'GPS Latitude DMS',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '50°3’46.15"N',
                    'value-formatted' => '50°3’46.15"N ',
                ],
                'gps-longitude-decimal-degree' => [
                    'title' => 'GPS Longitude Decimal Degree',
                    'format' => '%s',
                    'unit' => '°',
                    'unit-before' => null,
                    'value' => 14.4172,
                    'value-formatted' => '14.4172 °',
                ],
                'gps-longitude-direction' => [
                    'title' => 'GPS Longitude Direction',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'E',
                    'value-formatted' => 'E ',
                ],
                'gps-longitude-dms' => [
                    'title' => 'GPS Longitude',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '14°25’1.92"E',
                    'value-formatted' => '14°25’1.92"E ',
                ],
                'image-aperture' => [
                    'title' => 'Image Aperture',
                    'format' => '%.1f',
                    'unit' => null,
                    'unit-before' => 'F/',
                    'value' => 2.27,
                    'value-formatted' => 'F/2.27 ',
                ],
                'image-exposure-bias-value' => [
                    'title' => 'Image Exposure Bias Value',
                    'format' => '%d',
                    'unit' => ' steps',
                    'unit-before' => null,
                    'value' => 0,
                    'value-formatted' => '0  steps',
                ],
                'image-exposure-time' => [
                    'title' => 'Image Exposure Time',
                    'format' => '%s',
                    'unit' => ' s',
                    'unit-before' => null,
                    'value' => 0.01266,
                    'value-formatted' => '0.01266  s',
                ],
                'image-filename' => [
                    'title' => 'Image Filename',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'img-with-gps.jpg',
                    'value-formatted' => 'img-with-gps.jpg ',
                ],
                'image-focal-length' => [
                    'title' => 'Image Focal Length',
                    'format' => '%d',
                    'unit' => ' mm',
                    'unit-before' => null,
                    'value' => 3.05,
                    'value-formatted' => '3.05  mm',
                ],
                'image-height' => [
                    'title' => 'Image Height',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 480,
                    'value-formatted' => '480  px',
                ],
                'image-iso' => [
                    'title' => 'Image ISO',
                    'format' => '%d',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 100,
                    'value-formatted' => '100 ',
                ],
                'image-mime' => [
                    'title' => 'Image Mime',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'image/jpeg',
                    'value-formatted' => 'image/jpeg ',
                ],
                'image-size' => [
                    'title' => 'Image Size',
                    'format' => '%d',
                    'unit' => ' Bytes',
                    'unit-before' => null,
                    'value' => 105514,
                    'value-formatted' => '105514  Bytes',
                ],
                'image-size-human' => [
                    'title' => 'Image Size Human',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '103.04 kB',
                    'value-formatted' => '103.04 kB ',
                ],
                'image-width' => [
                    'title' => 'Image Width',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 640,
                    'value-formatted' => '640  px',
                ],
                'image-x-resolution' => [
                    'title' => 'Image X-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 72,
                    'value-formatted' => '72  dpi',
                ],
                'image-y-resolution' => [
                    'title' => 'Image Y-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 72,
                    'value-formatted' => '72  dpi',
                ],
            ]],

            /**
             * Image, Without GPS
             */
            [++$number, 'data/tests/images/properties/img-without-gps.jpg', [
                'device-manufacturer' => [
                    'title' => 'Device Manufacturer',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'SONY',
                    'value-formatted' => 'SONY ',
                ],
                'device-model' => [
                    'title' => 'Device Model',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'ILCE-7M2',
                    'value-formatted' => 'ILCE-7M2 ',
                ],
                'exif-version' => [
                    'title' => 'Exif Version',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '0231',
                    'value-formatted' => '0231 ',
                ],
                'image-aperture' => [
                    'title' => 'Image Aperture',
                    'format' => '%.1f',
                    'unit' => null,
                    'unit-before' => 'F/',
                    'value' => 4,
                    'value-formatted' => 'F/4 ',
                ],
                'image-exposure-bias-value' => [
                    'title' => 'Image Exposure Bias Value',
                    'format' => '%d',
                    'unit' => ' steps',
                    'unit-before' => null,
                    'value' => -1,
                    'value-formatted' => '-1  steps',
                ],
                'image-exposure-time' => [
                    'title' => 'Image Exposure Time',
                    'format' => '%s',
                    'unit' => ' s',
                    'unit-before' => null,
                    'value' => 0.00125,
                    'value-formatted' => '0.00125  s',
                ],
                'image-filename' => [
                    'title' => 'Image Filename',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'img-without-gps.jpg',
                    'value-formatted' => 'img-without-gps.jpg ',
                ],
                'image-focal-length' => [
                    'title' => 'Image Focal Length',
                    'format' => '%d',
                    'unit' => ' mm',
                    'unit-before' => null,
                    'value' => 50,
                    'value-formatted' => '50  mm',
                ],
                'image-height' => [
                    'title' => 'Image Height',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 480,
                    'value-formatted' => '480  px',
                ],
                'image-iso' => [
                    'title' => 'Image ISO',
                    'format' => '%d',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 50,
                    'value-formatted' => '50 ',
                ],
                'image-mime' => [
                    'title' => 'Image Mime',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'image/jpeg',
                    'value-formatted' => 'image/jpeg ',
                ],
                'image-size' => [
                    'title' => 'Image Size',
                    'format' => '%d',
                    'unit' => ' Bytes',
                    'unit-before' => null,
                    'value' => 200642,
                    'value-formatted' => '200642  Bytes',
                ],
                'image-size-human' => [
                    'title' => 'Image Size Human',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '195.94 kB',
                    'value-formatted' => '195.94 kB ',
                ],
                'image-width' => [
                    'title' => 'Image Width',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 720,
                    'value-formatted' => '720  px',
                ],
                'image-x-resolution' => [
                    'title' => 'Image X-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 240,
                    'value-formatted' => '240  dpi',
                ],
                'image-y-resolution' => [
                    'title' => 'Image Y-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 240,
                    'value-formatted' => '240  dpi',
                ],
            ]],

            /**
             * Image, Without GPS, Without focal
             */
            [++$number, 'data/tests/images/properties/img-without-focal.jpg', [
                'device-manufacturer' => [
                    'title' => 'Device Manufacturer',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'SONY',
                    'value-formatted' => 'SONY ',
                ],
                'device-model' => [
                    'title' => 'Device Model',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'ILCE-7M2',
                    'value-formatted' => 'ILCE-7M2 ',
                ],
                'exif-version' => [
                    'title' => 'Exif Version',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '0231',
                    'value-formatted' => '0231 ',
                ],
                'image-exposure-bias-value' => [
                    'title' => 'Image Exposure Bias Value',
                    'format' => '%d',
                    'unit' => ' steps',
                    'unit-before' => null,
                    'value' => -0.7,
                    'value-formatted' => '-0.7  steps',
                ],
                'image-exposure-time' => [
                    'title' => 'Image Exposure Time',
                    'format' => '%s',
                    'unit' => ' s',
                    'unit-before' => null,
                    'value' => 0.005,
                    'value-formatted' => '0.005  s',
                ],
                'image-filename' => [
                    'title' => 'Image Filename',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'img-without-focal.jpg',
                    'value-formatted' => 'img-without-focal.jpg ',
                ],
                'image-height' => [
                    'title' => 'Image Height',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 480,
                    'value-formatted' => '480  px',
                ],
                'image-iso' => [
                    'title' => 'Image ISO',
                    'format' => '%d',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 50,
                    'value-formatted' => '50 ',
                ],
                'image-mime' => [
                    'title' => 'Image Mime',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'image/jpeg',
                    'value-formatted' => 'image/jpeg ',
                ],
                'image-size' => [
                    'title' => 'Image Size',
                    'format' => '%d',
                    'unit' => ' Bytes',
                    'unit-before' => null,
                    'value' => 184780,
                    'value-formatted' => '184780  Bytes',
                ],
                'image-size-human' => [
                    'title' => 'Image Size Human',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '180.45 kB',
                    'value-formatted' => '180.45 kB ',
                ],
                'image-width' => [
                    'title' => 'Image Width',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 702,
                    'value-formatted' => '702  px',
                ],
                'image-x-resolution' => [
                    'title' => 'Image X-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 240,
                    'value-formatted' => '240  dpi',
                ],
                'image-y-resolution' => [
                    'title' => 'Image Y-Resolution',
                    'format' => '%d',
                    'unit' => ' dpi',
                    'unit-before' => null,
                    'value' => 240,
                    'value-formatted' => '240  dpi',
                ],
            ]],

            /**
             * Image, Without EXIF
             */
            [++$number, 'data/tests/images/properties/img-without-exif.jpg', [
                'image-height' => [
                    'title' => 'Image Height',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 480,
                    'value-formatted' => '480  px',
                ],
                'image-mime' => [
                    'title' => 'Image Mime',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => 'image/jpeg',
                    'value-formatted' => 'image/jpeg ',
                ],
                'image-size' => [
                    'title' => 'Image Size',
                    'format' => '%d',
                    'unit' => ' Bytes',
                    'unit-before' => null,
                    'value' => 113490,
                    'value-formatted' => '113490  Bytes',
                ],
                'image-size-human' => [
                    'title' => 'Image Size Human',
                    'format' => '%s',
                    'unit' => null,
                    'unit-before' => null,
                    'value' => '110.83 kB',
                    'value-formatted' => '110.83 kB ',
                ],
                'image-width' => [
                    'title' => 'Image Width',
                    'format' => '%d',
                    'unit' => ' px',
                    'unit-before' => null,
                    'value' => 679,
                    'value-formatted' => '679  px',
                ],
            ]],
        ];
    }
}
