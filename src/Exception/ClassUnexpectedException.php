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

use App\Exception\Base\BaseClassException;

/**
 * Class ClassUnexpectedException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class ClassUnexpectedException extends BaseClassException
{
    public const TEXT_PLACEHOLDER = 'Unexpected class "%s" given ("%s" expected).';

    /**
     * ClassNotFoundException constructor.
     *
     * @param class-string $class
     * @param class-string $expected
     */
    public function __construct(string $class, string $expected)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $class, $expected);

        parent::__construct($messageNonVerbose);
    }
}
