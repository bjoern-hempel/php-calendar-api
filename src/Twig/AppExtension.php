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

use App\Controller\Base\BaseController;
use App\Service\UrlService;
use App\Utils\FileNameConverter;
use Doctrine\ORM\Query\Expr\Base;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

    protected UrlGeneratorInterface $generator;

    /**
     * AppExtension constructor.
     *
     * @param KernelInterface $kernel
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(KernelInterface $kernel, UrlGeneratorInterface $generator)
    {
        $this->kernel = $kernel;

        $this->generator = $generator;
    }

    /**
     * Returns the TwigFilter[].
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
            new TwigFilter('url_absolute', [$this, 'urlAbsolute']),
        ];
    }

    /**
     * Returns the TwigFunction[].
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_encoded', [$this, 'pathEncoded']),
        ];
    }

    /**
     * TwigFilter: Twig filter preg_replace.
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
     * TwigFilter: Returns the orig path of given path.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function getPathOrig(string $path): string
    {
        $pathOrig = preg_replace('~\.[0-9]+\.([a-z]+)$~i', '.$1', $path);

        if (!is_string($pathOrig)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $this->checkPath($pathOrig);
    }

    /**
     * TwigFilter: Returns the 400 path of given path.
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
     * TwigFilter: Adds hash to the end of image path.
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
     * TwigFilter: Checks the given path and add .tmp if the file does not exists.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function checkPath(string $path): string
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

    /**
     * TwigFilter: Add url extensions.
     *
     * @param string $path
     * @return string
     */
    public function urlAbsolute(string $path): string
    {
        if (preg_match('~^http[s]?://~', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return sprintf('/%s', $path);
    }

    /**
     * TwigFunction: Returns encoded path.
     *
     * @param string $name
     * @param array<string, int|string> $parameters
     * @param bool $relative
     * @return string
     * @throws Exception
     */
    public function pathEncoded(string $name, array $parameters = [], bool $relative = false): string
    {
        $configName = sprintf('CONFIG_%s', strtoupper($name));

        $constantName = sprintf('%s::%s', BaseController::class, $configName);

        $config = constant($constantName);

        if ($config === null) {
            throw new Exception(sprintf('Constant name "%s" is not defined (%s:%d).', $constantName, __FILE__, __LINE__));
        }

        if (!is_array($config)) {
            throw new Exception(sprintf('Array data type expected (%s:%d).', __FILE__, __LINE__));
        }

        $encoded = UrlService::encode($config, $parameters);

        $nameEncoded = sprintf('%s_%s', $name, BaseController::KEY_NAME_ENCODED);

        $parametersEncoded = [
            BaseController::KEY_NAME_ENCODED => $encoded,
        ];

        return $this->generator->generate($nameEncoded, $parametersEncoded, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
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
        $path = preg_replace('~(\.[0-9]+)?(\.[a-z]+)$~i', '.tmp$1$2', $path);

        if (!is_string($path)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }
}
