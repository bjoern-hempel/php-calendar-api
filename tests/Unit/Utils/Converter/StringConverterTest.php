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

namespace App\Tests\Unit\Utils\Converter;

use App\Utils\Converter\StringConverter;
use PHPUnit\Framework\TestCase;

/**
 * Class StringConverterTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link StringConverter
 */
final class StringConverterTest extends TestCase
{
    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test SizeByte: $method
     * @param int $number
     * @param string $method
     * @param string $given
     * @param string $expected
     */
    public function wrapper(int $number, string $method, string $given, string $expected): void
    {
        /* Arrange */

        /* Act */
        $stringConverter = new StringConverter($given);
        $callback = [$stringConverter, $method];

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertContains($method, get_class_methods(StringConverter::class));
        $this->assertIsCallable($callback);
        $this->assertSame($expected, $stringConverter->{$method}());
    }

    /**
     * Data provider.
     *
     * @return array<int, array<int, string|int>>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [

            /**
             * getVariablized
             */
            [++$number, 'getVariablized', 'Test', 'Test'],
            [++$number, 'getVariablized', 'Test {test}, {foo} and {bar}.', 'Test `{test}`, `{foo}` and `{bar}`.'],
            [++$number, 'getVariablized', '2.5 Endpoint /api/v1/operators/{area}/{energy}/{zip}/{consumption}/{calculation_page}', '2.5 Endpoint /api/v1/operators/`{area}`/`{energy}`/`{zip}`/`{consumption}`/`{calculation_page}`'],
        ];
    }
}
