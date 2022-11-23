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

namespace App\Utils\Version;

use App\Container\File;
use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;
use App\Tests\Unit\Utils\Version\VersionTest;

/**
 * Class Version
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link VersionTest
 */
class Version
{
    final public const VALUE_LICENSE = 'Copyright (c) 2022 Björn Hempel';

    final public const VALUE_AUTHORS = [
        'Björn Hempel <bjoern@hempel.li>',
    ];

    final public const PATH_VERSION = 'VERSION';

    final public const INDEX_VERSION = 'version';

    final public const INDEX_LICENSE = 'license';

    final public const INDEX_AUTHORS = 'authors';

    /**
     * Version constructor.
     *
     * @param string $rootDir
     */
    public function __construct(protected string $rootDir)
    {
    }

    /**
     * Returns the version of this application.
     *
     * @return string
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function getVersion(): string
    {
        return (new File(sprintf('%s/%s', $this->rootDir, self::PATH_VERSION)))->getContentAsTextTrim();
    }

    /**
     * Returns the license of this application.
     *
     * @return string
     */
    public function getLicense(): string
    {
        return self::VALUE_LICENSE;
    }

    /**
     * Returns the author of this application.
     *
     * @return array<int, string>
     */
    public function getAuthors(): array
    {
        return self::VALUE_AUTHORS;
    }

    /**
     * Returns all information.
     *
     * @return array{version: string, license: string, authors: array<int, string>}
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function getAll(): array
    {
        return [
            self::INDEX_VERSION => $this->getVersion(),
            self::INDEX_LICENSE => $this->getLicense(),
            self::INDEX_AUTHORS => $this->getAuthors(),
        ];
    }
}
