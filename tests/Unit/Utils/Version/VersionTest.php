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

namespace App\Tests\Unit\Utils\Version;

use App\Container\File;
use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;
use App\Utils\Version\Version;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class VersionTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link Version
 */
final class VersionTest extends WebTestCase
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
     * Test wrapper (Version).
     *
     * @test
     *
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function wrapper(): void
    {
        /* Arrange */
        $versionString = (new File(Version::PATH_VERSION))->getContentAsTextTrim();
        $versionArray = [
            Version::INDEX_VERSION => $versionString,
            Version::INDEX_LICENSE => Version::VALUE_LICENSE,
            Version::INDEX_AUTHORS => Version::VALUE_AUTHORS,
        ];

        /* Act */
        $version = new Version(self::$projectDir);

        /* Assert */
        $this->assertEquals($versionString, $version->getVersion());
        $this->assertEquals($versionArray, $version->getAll());
    }
}
