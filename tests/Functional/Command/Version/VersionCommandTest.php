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

namespace App\Tests\Functional\Command\Version;

use App\Command\Version\VersionCommand;
use App\Container\File;
use App\Container\Json;
use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;
use App\Exception\TypeInvalidException;
use App\Exception\FunctionJsonEncodeException;
use App\Tests\Functional\Command\BaseCommandFunctionalTest;
use App\Utils\Validator\Validator;
use App\Utils\Version\Version;
use JsonException;

/**
 * Class VersionCommandTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link VersionCommand
 */
class VersionCommandTest extends BaseCommandFunctionalTest
{
    protected const PATH_API_KEY_SCHEMA = 'data/json/schema/other/version-verbose.schema.json';

    /**
     * @return void
     */
    public function doConfig(): void
    {
        $this
            ->setConfigUseParameterBag()
            ->setConfigUseCommand(
                VersionCommand::COMMAND_NAME,
                VersionCommand::class,
                fn () => [new Version($this->getProjectDir())]
            );
    }

    /**
     * Test wrapper (KeyCommand).
     *
     * @test
     * @throws FileNotFoundException
     * @throws TypeInvalidException
     * @throws FunctionJsonEncodeException
     * @throws JsonException
     * @throws FileNotReadableException
     */
    public function wrapper(): void
    {
        /* Arrange */
        $this->commandTester->execute(['--format' => 'json']);
        $json = new Json($this->commandTester->getDisplay());

        /* Act */
        $validator = new Validator($json, new File(self::PATH_API_KEY_SCHEMA));

        /* Assert */
        $this->assertTrue($validator->validate(), BaseCommandFunctionalTest::MESSAGE_JSON_RESPONSE_INVALID);
    }
}
