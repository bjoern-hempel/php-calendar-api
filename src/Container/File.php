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

namespace App\Container;

use App\Container\Base\BaseContainer;
use App\Exception\FileNotReadableException;
use App\Exception\FileNotFoundException;
use App\Exception\FunctionJsonEncodeException;
use App\Tests\Unit\Container\FileTest;
use Stringable;

/**
 * Class File
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link FileTest
 */
class File extends BaseContainer implements Stringable
{
    /**
     * File constructor.
     *
     * @param string $path
     * @param string|null $pathRoot
     */
    public function __construct(protected string $path, ?string $pathRoot = null)
    {
        if ($pathRoot !== null) {
            $this->setPath(sprintf('%s/%s', $pathRoot, $this->getPath()));
        }
    }

    /**
     * Returns the path of this container.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Returns the path of this container.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets the path of this container.
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns the filesize in Bytes.
     *
     * @return int
     * @throws FileNotFoundException
     */
    public function getFileSize(): int
    {
        $fileSize = filesize($this->path);

        if ($fileSize === false) {
            throw new FileNotFoundException($this->getPath());
        }

        return $fileSize;
    }

    /**
     * Checks if file exists.
     *
     * @return bool
     */
    public function exist(): bool
    {
        if (realpath($this->path) === false) {
            return false;
        }

        return true;
    }

    /**
     * Writes content to file.
     *
     * @param string|Json|null $data
     * @return bool
     * @throws FunctionJsonEncodeException
     */
    public function write(string|Json|null $data = null): bool
    {
        match (true) {
            is_null($data) => touch($this->getPath()),
            $data instanceof Json => file_put_contents($this->getPath(), $data->getJsonStringFormatted()),
            default => file_put_contents($this->getPath(), $data),
        };

        return true;
    }

    /**
     * Writes content to file and check existing file.
     *
     * @param string|Json|null $data
     * @return bool
     * @throws FileNotFoundException
     * @throws FunctionJsonEncodeException
     */
    public function writeAndCheck(string|Json|null $data = null): bool
    {
        if (!$this->exist()) {
            throw new FileNotFoundException($this->getPath());
        }

        return $this->write($data);
    }

    /**
     * Creates a file with given content, if file does not exist.
     *
     * @param string|Json|null $data
     * @return bool
     * @throws FileNotFoundException
     * @throws FunctionJsonEncodeException
     */
    public function createIfNotExists(string|Json|null $data = null): bool
    {
        if (!$this->exist()) {
            $this->write($data);
        }

        if (!$this->exist()) {
            throw new FileNotFoundException($this->getPath());
        }

        return true;
    }

    /**
     * Returns the real path of this container.
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function getRealPath(): string
    {
        $realPath = realpath($this->getPath());

        if ($realPath === false) {
            throw new FileNotFoundException($this->getPath());
        }

        return $realPath;
    }

    /**
     * Returns directory path of file.
     *
     * @return string
     */
    public function getDirectoryPath(): string
    {
        return dirname($this->getPath());
    }

    /**
     * Returns real directory path of file.
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function getRealDirectoryPath(): string
    {
        return dirname($this->getRealPath());
    }

    /**
     * Returns the file content as text.
     *
     * @return string
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function getContentAsText(): string
    {
        $realPath = $this->getRealPath();

        $content = file_get_contents($realPath);

        if ($content === false) {
            throw new FileNotReadableException($this->path);
        }

        return $content;
    }
}
