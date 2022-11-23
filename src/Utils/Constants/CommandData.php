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

namespace App\Utils\Constants;

/**
 * Class CommandData
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.2 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class CommandData
{
    final public const COMMAND_SCHEMA_DROP = 'doctrine:schema:drop --force --env=%(environment)s';

    final public const COMMAND_SCHEMA_CREATE = 'doctrine:schema:create --env=%(environment)s';

    final public const COMMAND_LOAD_FIXTURES = 'doctrine:fixtures:load -n --env=%(environment)s';
}
