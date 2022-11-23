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

use App\Exception\Base\BaseValueException;

/**
 * Class ValueInvalidException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @package App\Exception
 */
final class ValueInvalidException extends BaseValueException
{
    public const TEXT_PLACEHOLDER = 'Invalid value "%s" given. "%s" expected.';

    /**
     * InvalidValueException constructor.
     *
     * @param string $type
     * @param string $expected
     */
    public function __construct(string $type, string $expected)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $type, $expected);

        parent::__construct($messageNonVerbose);
    }
}
