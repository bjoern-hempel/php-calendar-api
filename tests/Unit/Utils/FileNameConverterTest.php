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

use App\Entity\Image;
use App\Utils\FileNameConverter;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class FileNameConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-01)
 * @package App\Tests\Unit\Utils
 */
final class FileNameConverterTest extends TestCase
{
    protected const PATH_ROOT = '/path/to/root';

    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test SizeConverter: $method
     * @param int $number
     * @param string $method
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $param3
     * @param mixed $param4
     * @param mixed $param5
     * @param mixed $param6
     * @param string $given
     * @param string $expected
     * @throws Exception
     */
    public function wrapper(int $number, string $method, mixed $param1, mixed $param2, mixed $param3, mixed $param4, mixed $param5, mixed $param6, string $given, string $expected): void
    {
        /* Arrange */

        /* Act */
        $fileNameConverter = new FileNameConverter($given, self::PATH_ROOT);
        $callback = [$fileNameConverter, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods($fileNameConverter));
        $this->assertIsCallable($callback);

        switch (true) {
            case $param6 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4, $param5, $param6));
                break;

            case $param5 !== null:
                /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
                $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4, $param5));
                break;

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
     * @return array<array<int, string|int|null|bool>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * Simple file name
             */
            [
                ++$number,
                'getFilename', /* method */
                null, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                800, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.800.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                true, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.tmp.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                800, /* width */
                true, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.800.tmp.data', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                800, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null,
                null,
                'data.data', /* given */
                'data.800.data', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                null, /* test */
                null, /* output mode */
                null,
                null,
                null,
                null,
                'data.data', /* given */
                'data.tmp.data', /* expected */
            ],

            /**
             * Simple file name (test)
             */
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                800, /* width */
                false, /* tmp */
                true, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.800.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                true, /* tmp */
                true, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.tmp.data', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                800, /* width */
                true, /* tmp */
                true, /* test */
                null, /* output mode */
                null, /* additional path */
                'data.data', /* given */
                'data.800.tmp.data', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                800, /* width */
                false, /* tmp */
                true, /* test */
                null, /* output mode */
                null,
                null,
                'data.data', /* given */
                'data.800.data', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                null, /* test */
                null, /* output mode */
                null,
                true,
                null,
                null,
                'data.data', /* given */
                'data.tmp.data', /* expected */
            ],

            /**
             * Complex file name (db file names)
             */
            [
                ++$number,
                'getFilename', /* method */
                null, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                null, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                null, /* test */
                null, /* output mode */
                '1', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                null, /* test */
                null, /* output mode */
                '1', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                null, /* test */
                null, /* output mode */
                '1', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                null, /* test */
                null, /* output mode */
                '1', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* type */
                null, /* width */
                null, /* tmp */
                null, /* test */
                null, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.BEACH.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.BEACH.400.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                null, /* test */
                null, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                null, /* test */
                null, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                null, /* test */
                null, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.BEACH.JPG', /* given */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.BEACH.tmp.JPG', /* expected */
            ],

            /**
             * Complex relative file name (db file names)
             */
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                '1', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                '1', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                '10', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/10/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                '10', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/10/2ccfdd526f.beach.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.JPG', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.JPG', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.JPG', /* expected */
            ],

            /**
             * Complex absolute file name (db file names)
             */
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                'abc', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/abc/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                'calendar-image', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/calendar-image/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* width */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.jpg', /* expected */
            ],

            /**
             * Complex absolute file name (db file names)
             */
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_SOURCE, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_TARGET, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                '1', /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_EXPECTED, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/expected/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilename', /* method */
                Image::PATH_TYPE_COMPARE, /* type */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additional path */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/compare/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTarget', /* method */
                null, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                '1', /* additional path */
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/target/1/2ccfdd526f.beach.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* width */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameTmp', /* method */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null,
                null,
                null,
                null,
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                '/path/to/root/data/tests/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.tmp.jpg', /* expected */
            ],
        ];
    }
}
