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

use App\Exception\Base\BaseKeyException;

/**
 * Class KeyExpiredException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class KeyExpiredException extends BaseKeyException
{
    public const TEXT_PLACEHOLDER = 'Given key "%s" is not valid (expired).';

    /**
     * KeyExpiredException constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $key);

        parent::__construct($messageNonVerbose);
    }
}
