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

use App\Exception\TypeInvalidException;
use App\Utils\Checker\CheckerArray;
use App\Utils\Checker\CheckerClass;
use PHPUnit\Framework\TestCase;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link CheckerArray
 */
final class CheckerArrayTest extends TestCase
{
    /**
     * Test wrapper (CheckerClass::checkX).
     *
     * @dataProvider dataProviderCheck
     *
     * @test
     * @testdox $number) Test CheckerClass::checkX
     * @param int $number
     * @param string $method
     * @param mixed $data
     * @param string $parameter
     * @param mixed $expected
     * @param class-string<TypeInvalidException>|null $exceptionClass
     * @throws TypeInvalidException
     * @link CheckerClass
     */
    public function wrapperCheck(int $number, string $method, mixed $data, string $parameter, mixed $expected, ?string $exceptionClass): void
    {
        /* Arrange */
        if ($exceptionClass !== null) {
            $this->expectException($exceptionClass);
        }

        /* Act */
        $checker = new CheckerArray($data);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertTrue(
            method_exists($checker, $method),
            sprintf('Class does not have method "%s".', $method)
        );
        $this->assertEquals($expected, $checker->{$method}($parameter));
    }

    /**
     * Data provider (CheckerClass::checkX).
     *
     * @return array<int, array{int, string, mixed, string, mixed, null|string}>
     * @link CheckerArray::checkPriceAbstract()
     */
    public function dataProviderCheck(): array
    {
        $number = 0;

        return [
            /* stdClass checks */
            [++$number, 'checkIndex', ['test' => 123], 'test', 123, null, ],
        ];
    }
}
