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

use App\Container\File;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class FileTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link File
 */
final class FileTest extends WebTestCase
{
    protected static string $projectDir;

    protected const NAME_KERNEL_PROJECT_DIR = 'kernel.project_dir';

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var ParameterBagInterface $parameterBag */
        $parameterBag = $container->get(ParameterBagInterface::class);

        if (!$parameterBag->has(self::NAME_KERNEL_PROJECT_DIR)) {
            throw new Exception(sprintf('Unable to get variable "%s" (%s:%d).', self::NAME_KERNEL_PROJECT_DIR, __FILE__, __LINE__));
        }

        self::$projectDir = strval($parameterBag->get(self::NAME_KERNEL_PROJECT_DIR));
    }

    /**
     * Test wrapper.
     *
     * @dataProvider dataProvider
     *
     * @test
     * @testdox $number) Test File::getRealPath
     * @param int $number
     * @param string $path
     * @param bool $valid
     * @throws Exception
     */
    public function wrapper(int $number, string $path, bool $valid): void
    {
        /* Assert */
        if (!$valid) {
            $this->expectException(Exception::class);
        }

        /* Arrange */
        $expected = sprintf('%s/%s', self::$projectDir, $path);

        /* Act */
        $realPath = (new File($path))->getRealPath();

        /* Assert */
        $this->assertIsNumeric($number); // To avoid phpmd warning.
        $this->assertEquals($expected, $realPath);
    }

    /**
     * Data provider.
     *
     * @return array<int, array{int, string, bool}>
     */
    public function dataProvider(): array
    {
        $number = 0;

        return [
            [++$number, 'data/json/schema/other/version-verbose.json', true, ],
            [++$number, 'data/json/schema/other/does-not-exist.json', false, ],
        ];
    }
}
