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

namespace App\Utils;

use App\Entity\Image;
use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class FileNameConverter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-28)
 * @package App\Utils
 */
class FileNameConverter
{
    protected string $filename;

    protected string $rootPath;

    protected bool $test;

    protected string $outputMode;

    public const MODE_OUTPUT_FILE = 'MODE_OUTPUT_FILE';

    public const MODE_OUTPUT_RELATIVE = 'MODE_OUTPUT_RELATIVE';

    public const MODE_OUTPUT_ABSOLUTE = 'MODE_OUTPUT_ABSOLUTE';

    public const PATH_IMAGES = Image::PATH_IMAGES;

    public const PATH_DATA = Image::PATH_DATA;

    /**
     * FileNameConverter constructor.
     *
     * @param string $filename
     * @param string $rootPath
     * @param bool $test
     * @param string $outputMode
     * @throws Exception
     */
    public function __construct(string $filename, string $rootPath = '', bool $test = false, string $outputMode = self::MODE_OUTPUT_FILE)
    {
        $this->filename = $this->filterFilename($filename);

        $this->rootPath = $rootPath;

        $this->test = $test;

        $this->outputMode = $outputMode;
    }

    /**
     * Filters the given filename.
     *
     * @param string $filename
     * @return string
     * @throws Exception
     */
    protected function filterFilename(string $filename): string
    {
        $filename = preg_replace('~^/~', '', $filename);

        if (!is_string($filename)) {
            throw new Exception(sprintf('Unable to replace given string (%s:%d).', __FILE__, __LINE__));
        }

        return $filename;
    }

    /**
     * Replaces the path by type.
     *
     * @param string $filename
     * @param string $type
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    protected static function replacePathType(string $filename, string $type, ?string $additionalPath = null): string
    {
        $search = sprintf('~([a-z0-9]{40,40}/)(%s|%s|%s|%s)(/)~', Image::PATH_TYPE_SOURCE, Image::PATH_TYPE_TARGET, Image::PATH_TYPE_COMPARE, Image::PATH_TYPE_EXPECTED);

        $path = $type;

        if ($additionalPath !== null) {
            $path = sprintf('%s/%s', $path, $additionalPath);
        }

        $replace = sprintf('$1%s$3', $path);

        $filename = preg_replace($search, $replace, $filename);

        if ($filename === null) {
            throw new Exception(sprintf('Unable to replace path (%s:%d).', __FILE__, __LINE__));
        }

        return $filename;
    }

    /**
     * Adds the width part to filename
     *
     * @param string $filename
     * @param int $width
     * @return string
     * @throws Exception
     */
    protected static function addFilenameWidth(string $filename, int $width): string
    {
        $filename = preg_replace('~\.([a-z]+)$~i', sprintf('.%d.$1', $width), $filename);

        if ($filename === null) {
            throw new Exception(sprintf('Unable to replace path (%s:%d).', __FILE__, __LINE__));
        }

        return $filename;
    }

    /**
     * Adds the tmp part to filename.
     *
     * @param string $filename
     * @return string
     * @throws Exception
     */
    protected static function addFilenameTmp(string $filename): string
    {
        $filename = preg_replace('~\.([a-z]+)$~i', '.tmp.$1', $filename);

        if ($filename === null) {
            throw new Exception(sprintf('Unable to replace path (%s:%d).', __FILE__, __LINE__));
        }

        return $filename;
    }

    /**
     * Adds the test part to filename (still unsupported).
     *
     * @param string $filename
     * @return string
     * @throws Exception
     */
    protected static function addFilenameTest(string $filename): string
    {
        return $filename;
    }

    /**
     * Adds relative path to filename.
     *
     * @param string $filename
     * @param bool $test
     * @return string
     */
    protected static function addPathRelative(string $filename, bool $test = false): string
    {
        $pathRelative = sprintf($test ? '%s/tests/%s' : '%s/%s', self::PATH_DATA, self::PATH_IMAGES);

        return sprintf('%s/%s', $pathRelative, $filename);
    }

    /**
     * Adds absolute path to filename.
     *
     * @param string $filename
     * @param string $rootPath
     * @param bool $test
     * @return string
     */
    #[Pure]
    protected static function addPathAbsolute(string $filename, string $rootPath = '', bool $test = false): string
    {
        return sprintf('%s/%s', $rootPath, self::addPathRelative($filename, $test));
    }

    /**
     * Returns the (raw) filename.
     *
     * @param string $type
     * @param int|null $width
     * @param bool $tmp
     * @param bool $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilename(string $type = Image::PATH_TYPE_SOURCE, ?int $width = null, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        $test = $test ?? $this->test;
        $outputMode = $outputMode ?? $this->outputMode;

        $filename = match ($type) {
            Image::PATH_TYPE_TARGET, Image::PATH_TYPE_EXPECTED, Image::PATH_TYPE_COMPARE => self::replacePathType($this->filename, $type, $additionalPath),
            default => $this->filename,
        };

        if ($width !== null) {
            $filename = self::addFilenameWidth($filename, $width);
        }

        if ($tmp) {
            $filename = self::addFilenameTmp($filename);
        }

        if ($test) {
            $filename = self::addFilenameTest($filename);
        }

        return match ($outputMode) {
            self::MODE_OUTPUT_FILE => $filename,
            self::MODE_OUTPUT_RELATIVE => self::addPathRelative($filename, $test),
            self::MODE_OUTPUT_ABSOLUTE => self::addPathAbsolute($filename, $this->rootPath, $test),
            default => throw new Exception(sprintf('Unsupported output mode (%s:%d).', __FILE__, __LINE__)),
        };
    }

    /**
     * Returns source filename
     *
     * @param int|null $width
     * @param bool $tmp
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameSource(?int $width = null, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_SOURCE, $width, $tmp, $test, $outputMode, $additionalPath);
    }

    /**
     * Returns target filename.
     *
     * @param int|null $width
     * @param bool $tmp
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameTarget(?int $width = null, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_TARGET, $width, $tmp, $test, $outputMode, $additionalPath);
    }

    /**
     * Returns target filename.
     *
     * @param int|null $width
     * @param bool $tmp
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameExpected(?int $width = null, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_EXPECTED, $width, $tmp, $test, $outputMode, $additionalPath);
    }

    /**
     * Returns target filename.
     *
     * @param int|null $width
     * @param bool $tmp
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameCompare(?int $width = null, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_COMPARE, $width, $tmp, $test, $outputMode, $additionalPath);
    }

    /**
     * Returns with filename.
     *
     * @param int $width
     * @param bool $tmp
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameWidth(int $width, bool $tmp = false, ?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_SOURCE, $width, $tmp, $test, $outputMode, $additionalPath);
    }

    /**
     * Returns tmp filename.
     *
     * @param bool|null $test
     * @param string|null $outputMode
     * @param string|null $additionalPath
     * @return string
     * @throws Exception
     */
    public function getFilenameTmp(?bool $test = null, ?string $outputMode = null, string $additionalPath = null): string
    {
        return $this->getFilename(Image::PATH_TYPE_SOURCE, null, true, $test, $outputMode, $additionalPath);
    }

    /**
     * @param string $filename
     * @return FileNameConverter
     */
    public function setFilename(string $filename): FileNameConverter
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Gets the root path.
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Sets the root path.
     *
     * @param string $rootPath
     * @return FileNameConverter
     */
    public function setRootPath(string $rootPath): FileNameConverter
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    /**
     * Gets test mode.
     *
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->test;
    }

    /**
     * Sets test mode.
     *
     * @param bool $test
     * @return FileNameConverter
     */
    public function setTest(bool $test): FileNameConverter
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Gets output mode.
     *
     * @return string
     */
    public function getOutputMode(): string
    {
        return $this->outputMode;
    }

    /**
     * Sets output mode.
     *
     * @param string $outputMode
     * @return FileNameConverter
     */
    public function setOutputMode(string $outputMode): FileNameConverter
    {
        $this->outputMode = $outputMode;

        return $this;
    }
}
