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

namespace App\Tests\Unit\Utils\Validator;

use App\Container\File;
use App\Container\Json;
use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;
use App\Exception\FunctionJsonEncodeException;
use App\Exception\TypeInvalidException;
use App\Utils\Constants\Constants;
use App\Utils\Validator\Validator;
use Exception;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class ValidatorTest extends TestCase
{
    /**
     * Test wrapper (Json::isJson).
     *
     * @dataProvider dataProviderIsJson
     *
     * @test
     * @testdox $number) Test Json::isJson
     * @param int $number
     * @param Json|File $data
     * @param Json|File $schema
     * @param bool $expected
     * @throws Exception
     */
    public function wrapper(int $number, Json|File $data, Json|File $schema, bool $expected): void
    {
        /* Arrange */
        $validator = new Validator($data, $schema);

        /* Act */
        $valid = $validator->validate();

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertEquals($expected, $valid);
    }

    /**
     * Data provider (Json::isJson).
     *
     * @return array<int, array{int, Json|File, Json|File, bool}>
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws TypeInvalidException
     * @throws JsonException
     */
    public function dataProviderIsJson(): array
    {
        $number = 0;

        return [
            /* Valid */
            [++$number, new Json('{}'), new Json('{}'), true, ],
            [++$number, new Json('{}'), new Json(Constants::SCHEMA_SIMPLE_OBJECT), true, ],
            [++$number, new Json('[]'), new Json('{}'), true, ],
            [++$number, new Json('[]'), new Json(Constants::SCHEMA_SIMPLE_OBJECT), true, ],
            [++$number, new Json('[1, 2, 3]'), new Json('{}'), true, ],
            [++$number, new Json('[1, 2, 3]'), new Json(Constants::SCHEMA_SIMPLE_OBJECT), true, ],
            [++$number, new File(Constants::PATH_JSON_VERSION_DATA), new Json('{}'), true, ],
            [++$number, new File(Constants::PATH_JSON_VERSION_DATA), new File(Constants::PATH_JSON_VERSION_SCHEMA), true, ],
            [++$number, new Json(new File(Constants::PATH_JSON_VERSION_DATA)), new File(Constants::PATH_JSON_VERSION_SCHEMA), true, ],

            /* Invalid */
            [++$number, new Json('[1, 2, 3]'), new File(Constants::PATH_JSON_VERSION_SCHEMA), false, ],
        ];
    }
}
