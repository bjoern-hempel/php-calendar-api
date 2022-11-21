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

namespace App\Service;

use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class VersionService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-22)
 * @package App\Command
 */
class VersionService
{
    public const PATH_VERSION_FILE = 'VERSION';

    public const PATH_REVISION_FILE = 'REVISION';

    protected KernelInterface $appKernel;

    /**
     * VersionService constructor
     *
     * @param KernelInterface $appKernel
     */
    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * Returns the version number from VERSION and REVISION file.
     *
     * @return string
     * @throws Exception
     */
    public function getVersion(): string
    {
        $versionFile = sprintf('%s/%s', $this->appKernel->getProjectDir(), self::PATH_VERSION_FILE);
        $revisionFile = sprintf('%s/%s', $this->appKernel->getProjectDir(), self::PATH_REVISION_FILE);

        if (!file_exists($versionFile)) {
            throw new Exception(sprintf('File was not found: %s (%s:%d)', $versionFile, __FILE__, __LINE__));
        }

        $versionNumber = file_get_contents($versionFile);
        $revisionNumber = null;

        if ($versionNumber === false) {
            throw new Exception(sprintf('Unable to get version file "%s" (%s:%d)', $versionFile, __FILE__, __LINE__));
        }

        $versionDate = filemtime($versionFile);

        if ($versionDate === false) {
            throw new Exception(sprintf('Unable to get date of file "%s" (%s:%d)', $versionFile, __FILE__, __LINE__));
        }

        if (file_exists($revisionFile)) {
            $revisionNumber = file_get_contents($revisionFile);

            if ($revisionNumber === false) {
                throw new Exception(sprintf('Unable to get version file "%s" (%s:%d)', $revisionFile, __FILE__, __LINE__));
            }
        }

        if ($revisionNumber !== null && $revisionNumber !== false) {
            return sprintf('v%s (%s, %s)', $versionNumber, $revisionNumber, date('Y-m-d H:m:s', $versionDate));
        }

        return sprintf('v%s (%s)', $versionNumber, date('Y-m-d H:m:s', $versionDate));
    }
}
