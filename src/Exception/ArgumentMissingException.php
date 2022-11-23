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
 * Class ArgumentMissingException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class ArgumentMissingException extends BaseException
{
    public const TEXT_PLACEHOLDER = 'Argument missing ("%s").';

    /**
     * ArgumentMissingException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $name);

        parent::__construct($messageNonVerbose);
    }
}
