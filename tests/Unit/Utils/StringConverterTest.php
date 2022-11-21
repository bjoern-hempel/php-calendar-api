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
                $this->assertSame($expected, call_user_func($callback, $param1, $param2));
                break;

            case $param1 !== null:
                $this->assertSame($expected, call_user_func($callback, $param1));
                break;

            default:
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
            [++$number, 'convertDateTimeFormat', '2022:05:01 20:12:35', 'Y-m-d H:i:s', '2022-05-01 20:12:35'],
            [++$number, 'convertDateTimeFormat', '2022:05:01 20:12:35', 'c', '2022-05-01T20:12:35+00:00'],
        ];
    }
}
