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

        match (true) {
            $param6 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4, $param5, $param6)),
            $param5 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4, $param5)),
            $param4 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3, $param4)),
            $param3 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2, $param3)),
            $param2 !== null => $this->assertSame($expected, call_user_func($callback, $param1, $param2)),
            $param1 !== null => $this->assertSame($expected, call_user_func($callback, $param1)),
            default => $this->assertSame($expected, call_user_func($callback)),
        };
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
                Image::PATH_TYPE_SOURCE, /* type */
                false, /* tmp */
                true, /* test */
                null, /* output mode */
                null, /* additionalPath */
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
                400, /* width */
                Image::PATH_TYPE_SOURCE, /* type */
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
                400, /* width */
                Image::PATH_TYPE_SOURCE, /* type */
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
                400, /* width */
                Image::PATH_TYPE_SOURCE, /* type */
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
                Image::PATH_TYPE_SOURCE, /* type */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additionalPath */
                'cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.jpg', /* given */
                'data/images/cf6b37d2b5f805a0f76ef2b3610eff7a705a2290/source/2ccfdd526f.beach.400.jpg', /* expected */
            ],
            [
                ++$number,
                'getFilenameWidth', /* method */
                400, /* width */
                Image::PATH_TYPE_SOURCE, /* type */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_RELATIVE, /* output mode */
                null, /* additionalPath */
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
                Image::PATH_TYPE_SOURCE, /* type */
                false, /* tmp */
                false, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additionalPath */
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
                Image::PATH_TYPE_SOURCE, /* type */
                false, /* tmp */
                true, /* test */
                FileNameConverter::MODE_OUTPUT_ABSOLUTE, /* output mode */
                null, /* additionalPath */
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
