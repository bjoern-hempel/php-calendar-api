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

namespace App\Exception;

use Exception;

/**
 * Abstract class BasePHPCalendarApiException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-20)
 * @package App\Exception
 */
abstract class BasePHPCalendarApiException extends Exception
{
    /* General codes */
    protected const RETURN_BASE = 100;

    /**
     * Returns the return code of current exception.
     *
     * @return int
     */
    abstract public function getReturnCode(): int;
}
