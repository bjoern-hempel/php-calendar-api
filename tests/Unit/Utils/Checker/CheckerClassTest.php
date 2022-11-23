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

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Exception\TypeInvalidException;
use App\Exception\ClassUnexpectedException;
use App\Utils\Checker\CheckerClass;
use PHPUnit\Framework\TestCase;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link CheckerClass
 */
final class CheckerClassTest extends TestCase
{
    /**
     * @dataProvider dataProviderCheck
     *
     * @test
     * @testdox $number) Test CheckerClass::checkX
     * @param int $number
     * @param string $method
     * @param mixed $data
     * @param class-string|null $className
     * @param class-string<TypeInvalidException>|null $exceptionClass
     * @throws TypeInvalidException
     * @link CheckerClass
     */
    public function wrapperCheck(int $number, string $method, mixed $data, string|null $className, string|null $exceptionClass): void
    {
        /* Arrange */
        if ($exceptionClass !== null) {
            $this->expectException($exceptionClass);
        }

        /* Act */
        $checker = new CheckerClass($data);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertTrue(
            method_exists($checker, $method),
            sprintf('Class does not have method "%s".', $method)
        );
        match (true) {
            $className !== null => $this->assertEquals($data, $checker->{$method}($className)),
            default => $this->assertEquals($data, $checker->{$method}())
        };
    }

    /**
     * Data provider (CheckerClass::checkX).
     *
     * @return array<int, array{int, string, mixed, class-string|null, null|string}>
     * @link CheckerClass::checkGiven()
     * @link CheckerClass::checkStdClass()
     */
    public function dataProviderCheck(): array
    {
        $number = 0;

        return [
            /* class given checks */
            [++$number, 'checkGiven', new Calendar(), Calendar::class, null, ],
            [++$number, 'checkGiven', 'calendar', Calendar::class, TypeInvalidException::class, ],
            [++$number, 'checkGiven', new CalendarImage(), Calendar::class, ClassUnexpectedException::class, ],

            /* stdClass checks */
            [++$number, 'checkStdClass', (object)[], null, null, ],
            [++$number, 'checkStdClass', null, null, TypeInvalidException::class, ],
        ];
    }
}
