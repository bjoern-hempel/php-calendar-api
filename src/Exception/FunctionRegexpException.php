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

use App\Exception\Base\BaseFunctionException;

/**
 * Class FunctionRegexpException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class FunctionRegexpException extends BaseFunctionException
{
    public const TEXT_PLACEHOLDER = 'The given value "%s" does not match the regexp pattern "%s".';

    /**
     * RegexpException constructor.
     *
     */
    public function __construct(string $value, string $regexp)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $value, $regexp);

        parent::__construct($messageNonVerbose);
    }
}
