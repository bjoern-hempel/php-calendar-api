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
     * @param bool $short
     * @param string $expected
     */
    public function wrapperEncode(int $number, string $method, array $config, array $given, bool $withPath, bool $short, string $expected): void
    {
        /* Arrange */

        /* Act */
        $callback = [UrlService::class, $method];

        /* Assert */
        $this->assertContains($method, get_class_methods(UrlService::class));
        $this->assertIsCallable($callback);
        $this->assertSame($expected, call_user_func($callback, $config, $given, $withPath, $short));
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
             * build (without path, standard)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], false, false, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, false, 'da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52'],

            /**
             * build (without path, short)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ], false, true, 'da4b9237/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, true, 'da4b9237/2/52'],

            /**
             * build (with path, standard)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], true, false, 'calendar/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, false, 'calendar/detail/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52'],

            /**
             * build (with path, short)
             */
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ], true, true, 'c/da4b9237/2/5'],
            [++$number, 'build', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, true, 'd/da4b9237/2/52'],

            /**
             * encode (without path, standard)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], false, false, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, false, 'ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy'],

            /**
             * encode (without path, short)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ], false, true, 'ZGE0YjkyMzcvMi81'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ], false, true, 'ZGE0YjkyMzcvMi81Mg--'],

            /**
             * encode (with path, standard)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ], true, false, 'calendar/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzU-'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, false, 'calendar/detail/ZGE0YjkyMzdiYWNjY2RmMTljMDc2MGNhYjdhZWM0YTgzNTkwMTBiMC8yLzUy'],

            /**
             * encode (with path, short)
             */
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ], true, true, 'c/ZGE0YjkyMzcvMi81'],
            [++$number, 'encode', BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ], true, true, 'd/ZGE0YjkyMzcvMi81Mg--'],
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
             * parse (without path, standard)
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
             * parse (without path, short)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'da4b9237/2/5', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'da4b9237/2/52', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * decode (without path, standard)
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
             * decode (without path, short)
             */
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_INDEX, 'ZGE0YjkyMzcvMi81', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'ZGE0YjkyMzcvMi81Mg--', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (with path, standard)
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
             * parse (with path, short)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'calendar/da4b9237/2/5', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'calendar/detail/da4b9237/2/52', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * decode (with path, standard)
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
             * decode (with path, short)
             */
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_INDEX, 'c/ZGE0YjkyMzcvMi81', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'd/ZGE0YjkyMzcvMi81Mg--', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (with path and beginning /, standard)
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
             * parse (with path and beginning /, short)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, '/c/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, '/d/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (full url, parse, standard)
             */
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_INDEX, 'https://twelvepics.com/c/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/5', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'parse', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'https://twelvepics.com/d/da4b9237bacccdf19c0760cab7aec4a8359010b0/2/52', [
                'hash' => 'da4b9237bacccdf19c0760cab7aec4a8359010b0',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],

            /**
             * parse (full url, decode standard)
             */
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_INDEX, 'https://twelvepics.com/c/ZGE0YjkyMzcvMi81', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarId' => 5,
            ]],
            [++$number, 'decode', BaseController::CONFIG_APP_CALENDAR_DETAIL, 'https://twelvepics.com/d/ZGE0YjkyMzcvMi81Mg--', [
                'hash' => 'da4b9237',
                'userId' => 2,
                'calendarImageId' => 52,
            ]],
        ];
    }
}
