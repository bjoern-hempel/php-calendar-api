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

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ConfigurationNotFoundException
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-21)
 * @package App\Exception
 */
final class ConfigurationNotFoundException extends HttpException
{
    public const TEXT_PLACEHOLDER = 'Configuration "%s" was not found.';

    public const DEFAULT_CONFIGURATION_NAME = 'not_given';

    public const CODE = 404;

    /**
     * DocumentNotFoundException constructor.
     *
     * @param ?string $configurationName
     */
    public function __construct(string $configurationName = null)
    {
        $message = sprintf(self::TEXT_PLACEHOLDER, $configurationName ?? self::DEFAULT_CONFIGURATION_NAME);

        parent::__construct(self::CODE, $message);
    }
}
