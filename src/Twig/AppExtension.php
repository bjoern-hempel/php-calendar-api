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

namespace App\Twig;

use App\Utils\FileNameConverter;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * AppExtension class
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-03-05)
 * @package App\Entity
 */
class AppExtension extends AbstractExtension
{
    protected KernelInterface $kernel;

    protected FileNameConverter $fileNameConverter;

    /**
     * AppExtension constructor.
     *
     * @param KernelInterface $kernel
     * @throws Exception
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns the TwigFilter.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('preg_replace', [$this, 'pregReplace']),
            new TwigFilter('path_orig', [$this, 'getPathOrig']),
            new TwigFilter('path_400', [$this, 'getPath400']),
            new TwigFilter('add_hash', [$this, 'addHash']),
            new TwigFilter('check_path', [$this, 'checkPath']),
        ];
    }

    /**
     * Twig filter preg_replace.
     *
     * @param string $subject
     * @param string $pattern
     * @param string $replacement
     * @return string
     * @throws Exception
     */
    public function pregReplace(string $subject, string $pattern, string $replacement): string
    {
        $replaced = preg_replace($pattern, $replacement, $subject);

        if (!is_string($replaced)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $replaced;
    }

    /**
     * Returns the orig path of given path.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function getPathOrig(string $path): string
    {
        $pathOrig = preg_replace('~\.[0-9]+\.([a-z]+)$~', '.$1', $path);

        if (!is_string($pathOrig)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $this->checkPath($pathOrig);
    }

    /**
     * Returns the 400 path of given path.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function getPath400(string $path): string
    {
        return $this->checkPath($path);
    }

    /**
     * Adds hash to the end of image path.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function addHash(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new Exception(sprintf('Image "%s" was not found (%s:%d).', $path, __FILE__, __LINE__));
        }

        $md5 = md5_file($fullPath);

        if ($md5 === false) {
            throw new Exception(sprintf('Unable to calculate md5 hash from file "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        return sprintf('%s?%s', $path, $md5);
    }

    /**
     * Returns the full path.
     *
     * @param string $path
     * @return string
     */
    protected function getFullPath(string $path): string
    {
        return sprintf('%s/%s', $this->kernel->getProjectDir(), $path);
    }

    /**
     * Add tmp part to given file.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    protected function addTmp(string $path): string
    {
        $path = preg_replace('~(\.[0-9]+)?(\.[a-z]+)$~', '.tmp$1$2', $path);

        if (!is_string($path)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Checks the given path and add .tmp if the file does not exists.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    protected function checkPath(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (file_exists($fullPath)) {
            return $path;
        }

        $path = $this->addTmp($path);
        $fullPath = $this->getFullPath($path);

        if (file_exists($fullPath)) {
            return $path;
        }

        throw new Exception(sprintf('Unable to find image "%s" (%s:%d).', $fullPath, __FILE__, __LINE__));
    }
}
