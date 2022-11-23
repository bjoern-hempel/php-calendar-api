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

namespace App\Tests\Unit\Container;

use App\Container\Json;
use App\Exception\TypeInvalidException;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class JsonTest extends TestCase
{
    /**
     * Test wrapper (Json::getArray).
     *
     * @dataProvider dataProviderGetArray
     *
     * @test
     * @testdox $number) Test Json::getArray
     * @param int $number
     * @param string|object|array<int|string, mixed> $data
     * @param class-string<TypeInvalidException>|array<int|string, mixed> $expected
     * @throws Exception
     */
    public function wrapperGetArray(int $number, string|object|array $data, array|string $expected): void
    {
        /* Arrange */
        if (is_string($expected)) {
            $this->expectException($expected);
        }

        /* Act */
        $json = new Json($data);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertEquals($expected, $json->getArray());
    }

    /**
     * Data provider (Json::getArray).
     *
     * @return array<int, array{int, string|object|array<int|string, mixed>, array<int|string, mixed>|string}>
     */
    public function dataProviderGetArray(): array
    {
        $number = 0;

        return [
            /* Valid values */
            [++$number, '[]', [], ],
            [++$number, '[1, 2, 3]', [1, 2, 3, ], ],
            [++$number, '{}', [], ],
            [++$number, '{"1": 1, "2": 2}', ["1" => 1, "2" => 2, ], ],
            [++$number, [], [], ],
            [++$number, [1, 2, 3, ], [1, 2, 3, ], ],
            [++$number, ["1" => 1, "2" => 2, ], ["1" => 1, "2" => 2, ], ],
            [++$number, (object)[], [], ],
            [++$number, (object)[1, 2, 3, ], [1, 2, 3, ], ],
            [++$number, (object)["1" => 1, "2" => 2, ], ["1" => 1, "2" => 2, ], ],
            [++$number, new Json('[]'), [], ],
            [++$number, new Json('[1, 2, 3]'), [1, 2, 3, ], ],
            [++$number, new Json('{}'), [], ],
            [++$number, new Json('{"1": 1, "2": 2}'), ["1" => 1, "2" => 2, ], ],

            /* Invalid values */
            [++$number, '', TypeInvalidException::class, ],
            [++$number, '{', TypeInvalidException::class, ],
            [++$number, '{123:123}', TypeInvalidException::class, ],
            [++$number, '{"abc": "123"]', TypeInvalidException::class, ],
        ];
    }

    /**
     * Test wrapper (Json::addJson).
     *
     * @dataProvider dataProviderAddJson
     *
     * @test
     * @testdox $number) Test Json::addJson
     * @param int $number
     * @param string|object|array<int|string, mixed> $data
     * @param string|object|array<int|string, mixed> $dataAdd
     * @param string|array<int, string>|null $path
     * @param class-string<TypeInvalidException>|array<int|string, mixed> $expected
     * @throws Exception
     */
    public function wrapperAddJson(int $number, string|object|array $data, string|object|array $dataAdd, string|array|null $path, array|string $expected): void
    {
        /* Arrange */
        if (is_string($expected)) {
            $this->expectException($expected);
        }

        /* Act */
        $json = new Json($data);
        $json->addJson($dataAdd, $path);

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertEquals($expected, $json->getArray());
    }

    /**
     * Data provider (Json::addJson).
     *
     * @return array<int, array{int, string|object|array<int|string, mixed>, string|object|array<int|string, mixed>, string|array<int, string>|null, array<int|string, mixed>|string}>
     */
    public function dataProviderAddJson(): array
    {
        $number = 0;

        return [
            [++$number, '[]', [], null, [], ],
            [++$number, '[]', (object)[], null, [], ],
            [++$number, '[]', '[]', null, [], ],
            [++$number, '[]', new Json('[]'), null, [], ],

            [++$number, '[1, 2, 3]', [], null, [1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', (object)[], null, [1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', '[]', null, [1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', new Json('[]'), null, [1, 2, 3, ], ],

            [++$number, '[1, 2, 3]', [1, 2, 3, ], null, [1, 2, 3, 1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', (object)[1, 2, 3, ], null, [1, 2, 3, 1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', '[1, 2, 3]', null, [1, 2, 3, 1, 2, 3, ], ],
            [++$number, '[1, 2, 3]', new Json('[1, 2, 3]'), null, [1, 2, 3, 1, 2, 3, ], ],

            [++$number, '[1, 2, 3]', ["first" => 1, "second" => 2, "third" => 3, ], null, [1, 2, 3, "first" => 1, "second" => 2, "third" => 3, ], ],
            [++$number, '[1, 2, 3]', (object)["first" => 1, "second" => 2, "third" => 3, ], null, [1, 2, 3, "first" => 1, "second" => 2, "third" => 3, ], ],
            [++$number, '[1, 2, 3]', '{"first": 1, "second": 2, "third": 3}', null, [1, 2, 3, "first" => 1, "second" => 2, "third" => 3, ], ],
            [++$number, '[1, 2, 3]', new Json('{"first": 1, "second": 2, "third": 3}'), null, [1, 2, 3, "first" => 1, "second" => 2, "third" => 3, ], ],

            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], null, ["version" => "1.0.0", "data" => [], "field1" => "1", "field2" => 2, ], ],
            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], '', ["version" => "1.0.0", "data" => [], "field1" => "1", "field2" => 2, ], ],
            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], 'data', ["version" => "1.0.0", "data" => ["field1" => "1", "field2" => 2, ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], 'data.test', ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => 2, ], ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], ['data', ], ["version" => "1.0.0", "data" => ["field1" => "1", "field2" => 2, ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => 2, ], ['data', 'test', ], ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => 2, ], ], ], ],

            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', null, ["version" => "1.0.0", "data" => [], "field1" => "1", "field2" => 2, ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', '', ["version" => "1.0.0", "data" => [], "field1" => "1", "field2" => 2, ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', 'data', ["version" => "1.0.0", "data" => ["field1" => "1", "field2" => 2, ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', 'data.test', ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => 2, ], ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', ['data', ], ["version" => "1.0.0", "data" => ["field1" => "1", "field2" => 2, ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": 2}', ['data', 'test', ], ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => 2, ], ], ], ],

            [++$number, '{"version": "1.0.0", "data": []}', ["field1" => "1", "field2" => [1, 2, [3, 4, 5, ], ] ], ['data', 'test', ], ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => [1, 2, [3, 4, 5, ], ], ], ], ], ],
            [++$number, '{"version": "1.0.0", "data": []}', '{"field1": "1", "field2": [1, 2, [3, 4, 5]]}', ['data', 'test', ], ["version" => "1.0.0", "data" => ["test" => ["field1" => "1", "field2" => [1, 2, [3, 4, 5, ], ], ], ], ], ],

            [++$number, '{"version": "1.0.0", "data": []}', '{"version": "1.0.0", "data": []}', null, ["version" => "1.0.0", "data" => [], ], ],
        ];
    }
}
