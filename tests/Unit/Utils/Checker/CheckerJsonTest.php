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

namespace App\Tests\Unit\Utils\Checker;

use App\Container\File;
use App\Exception\TypeInvalidException;
use App\Utils\Checker\CheckerJson;
use PHPUnit\Framework\TestCase;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link CheckerJson
 */
final class CheckerJsonTest extends TestCase
{
    /**
     * Test wrapper (Checker::checkX).
     *
     * @dataProvider dataProviderCheck
     *
     * @test
     * @testdox $number) Test Checker::checkX
     * @param int $number
     * @param string $method
     * @param mixed $data
     * @param class-string<TypeInvalidException>|null $exceptionClass
     * @throws TypeInvalidException
     */
    public function wrapperCheck(int $number, string $method, mixed $data, ?string $exceptionClass): void
    {
        /* Arrange */
        if ($exceptionClass !== null) {
            $this->expectException($exceptionClass);
        }

        /* Act */
        $checker = new CheckerJson($data);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertTrue(
            method_exists($checker, $method),
            sprintf('Class does not have method "%s".', $method)
        );
        $this->assertEquals($data, $checker->{$method}());
    }

    /**
     * Data provider (Checker::checkX).
     *
     * @return array<int, array{int, string, mixed, null|string}>
     * @link CheckerJson::checkJson()
     */
    public function dataProviderCheck(): array
    {
        $number = 0;

        return [
            /* JSON checks */
            [++$number, 'checkJson', '{}', null, ],
            [++$number, 'checkJson', null, TypeInvalidException::class, ],
        ];
    }

    /**
     * Test wrapper (Checker::isX).
     *
     * @dataProvider dataProviderIs
     *
     * @test
     * @testdox $number) Test Checker::isX
     * @param int $number
     * @param string $method
     * @param mixed $data
     * @param bool $expected
     * @throws TypeInvalidException
     */
    public function wrapperIs(int $number, string $method, mixed $data, bool $expected): void
    {
        /* Arrange */

        /* Act */
        $checker = new CheckerJson($data);
        $isValue = (new CheckerJson($data))->{$method}();

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertTrue(
            method_exists($checker, $method),
            sprintf('Class does not have method "%s".', $method)
        );
        $this->assertEquals($expected, $isValue);
    }

    /**
     * Data provider (Checker::isX).
     *
     * @return array<int, array{int, string, mixed, bool}>
     * @link CheckerJson::isJson()
     */
    public function dataProviderIs(): array
    {
        $number = 0;

        return [
            /* JSON checks */
            [++$number, 'isJson', '{}', true, ],
            [++$number, 'isJson', '{"abc": "123"}', true, ],
            [++$number, 'isJson', '{"abc": [1, 2, 3]}', true, ],
            [++$number, 'isJson', '', false, ],
            [++$number, 'isJson', '{', false, ],
            [++$number, 'isJson', '{123:123}', false, ],
            [++$number, 'isJson', '{"abc": "123"]', false, ],
            [++$number, 'isJson', [], false, ],
            [++$number, 'isJson', new File('path'), false, ],
            [++$number, 'isJson', false, false, ],
            [++$number, 'isJson', null, false, ],
        ];
    }
}
