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

use App\Exception\Base\BaseApiException;

/**
 * Class ApiKeyFormatNotValidException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class ApiKeyFormatNotValidException extends BaseApiException
{
    public const TEXT_PLACEHOLDER = 'Given api key format "%s" is not valid.';

    /**
     * ApiKeyFormatNotValidException constructor.
     *
     * @param string $apiKey
     */
    public function __construct(protected string $apiKey)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $apiKey);

        parent::__construct($messageNonVerbose);
    }
}
