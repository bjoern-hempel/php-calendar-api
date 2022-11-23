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

use App\Exception\Base\BaseOptionException;

/**
 * Class OptionInvalidException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class OptionInvalidException extends BaseOptionException
{
    public const TEXT_PLACEHOLDER = 'Invalid option "%s" given. "%s" expected.';

    /**
     * OptionInvalidException constructor.
     *
     * @param string $given
     * @param string[] $expected
     */
    public function __construct(string $given, array $expected)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $given, implode(', ', $expected));

        parent::__construct($messageNonVerbose);
    }
}
