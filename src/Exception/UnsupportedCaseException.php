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

use App\Exception\Base\BaseException;

/**
 * Class UnsupportedCaseException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class UnsupportedCaseException extends BaseException
{
    public const TEXT_PLACEHOLDER = 'Unsupported case ("%s").';

    /**
     * UnsupportedCaseException constructor.
     *
     * @param string $case
     */
    public function __construct(string $case)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $case);

        parent::__construct($messageNonVerbose);
    }
}
