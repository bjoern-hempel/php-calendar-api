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

namespace App\Container\Base;

use App\Container\Json;
use App\Exception\FileNotFoundException;
use App\Exception\FileNotReadableException;
use App\Exception\FunctionJsonEncodeException;
use App\Exception\TypeInvalidException;
use JsonException;

/**
 * Class BaseContainer
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
abstract class BaseContainer
{
    /**
     * Returns the file content as text.
     *
     * @return string
     */
    abstract public function getContentAsText(): string;

    /**
     * Returns the file content as text (trimmed).
     *
     * @return string
     */
    public function getContentAsTextTrim(): string
    {
        return trim($this->getContentAsText());
    }

    /**
     * Returns the file content as json.
     *
     * @return Json
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws TypeInvalidException
     * @throws JsonException
     */
    public function getContentAsJson(): Json
    {
        return new Json($this->getContentAsText());
    }
}
