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

use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;

/**
 * Class FileSerialized
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @package App\Container
 */
class FileSerialized extends File
{
    protected string $pathSerialized;

    protected const TEMPLATE_PATH_SERIALIZED = '%s.serialized';

    /**
     * File constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->pathSerialized = sprintf(self::TEMPLATE_PATH_SERIALIZED, $path);

        parent::__construct($path);
    }

    /**
     *
     *
     * @return string
     */
    public function getPathSerialized(): string
    {
        return $this->pathSerialized;
    }

    /**
     * Returns the filesize in Bytes.
     *
     * @return int
     * @throws FileNotFoundException
     */
    public function getFileSizeSerialized(): int
    {
        $fileSize = filesize($this->pathSerialized);

        if ($fileSize === false) {
            throw new FileNotFoundException($this->getPath());
        }

        return $fileSize;
    }

    /**
     * Returns the unserialized element of file content.
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    public function getUnserialized(): mixed
    {
        $fileUnserialized = new File($this->pathSerialized);

        $content = $fileUnserialized->getContentAsText();

        return unserialize($content);
    }
}
