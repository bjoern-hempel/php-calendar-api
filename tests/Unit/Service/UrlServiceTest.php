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

namespace App\Tests\Unit\Service;

use App\Controller\Base\BaseController;
use App\Service\UrlService;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlServiceTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-19)
 * @package App\Tests\Unit\Service
 */
final class UrlServiceTest extends TestCase
{
    /**
     * Test wrapper (build, encode).
     *
     * @dataProvider dataProviderEncode
     *
     * @test
     * @testdox $number) Test UrlService: $method
     * @param int $number
     * @param string $method
     * @param array<string, string> $config
     * @param array<int, string|int> $given
     * @param bool $withPath
     * @param string $expected
     */
    public function wrapperEncode(int $number, string $method, array $config, array $given, bool $withPath, string $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [UrlService::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(UrlService::class));
        $this->assertIsCallable($callback);
        /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
        $this->assertSame($expected, call_user_func($callback, $config, $given, $withPath));
    }

    /**
     * Test wrapper (parse, decode).
     *
     * @dataProvider dataProviderDecode
     *
     * @test
     * @testdox $number) Test UrlService: $method
     * @param int $number
     * @param string $method
     * @param array<string, string> $config
     * @param string $path
     * @param array<string, string> $expected
     */
    public function wrapperDecode(int $number, string $method, array $config, string $path, array $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [UrlService::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(UrlService::class));
        $this->assertIsCallable($callback);
        /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
        $this->assertSame($expected, call_user_func($callback, $config, $path));
    }

    /**
     * Data provider (build, encode).
     *
     * @return array<int, array<int, array<string, array<string, string>|int|string>|bool|int|string>>
     */
    public function dataProviderEncode(): array
    {
        $number = 0;

        return [

            /**
             * build (without path)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], false, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52'],

            /**
             * build (with path)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], true, 'calendar/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, 'calendar/detail/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52'],

            /**
             * encode (without path)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], false, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy'],

            /**
             * encode (with path)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], true, 'calendar/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, 'calendar/detail/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy'],
        ];
    }

    /**
     * Data provider (parse, decode).
     *
     * @return array<int, array<int, array<string, array<string, string>|int|string>|int|string>>
     */
    public function dataProviderDecode(): array
    {
        $number = 0;

        return [

            /**
             * parse (without path)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * decode (without path)
             */
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_INDEX, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (with path)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'calendar/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'calendar/detail/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * decode (with path)
             */
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_INDEX, 'calendar/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'calendar/detail/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (with path and beginning /)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, '/calendar/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, '/calendar/detail/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (full url)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'https://calendar.ixno.de/calendar/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'https://calendar.ixno.de/calendar/detail/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],
        ];
    }
}
