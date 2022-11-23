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
 * Class Constants
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class Constants
{
    final public const LINES_MINUS_ONE = -1;

    final public const INDENTION_2 = 2;

    final public const JSON_SCHEMA_DRAFT_07 = 'http://json-schema.org/draft-07/schema#';

    final public const SCHEMA_SIMPLE_OBJECT = [
        "type" => "object"
    ];

    final public const SCHEMA_SIMPLE_ARRAY = [
        "type" => "array"
    ];

    final public const PATH_JSON_VERSION_DATA = 'data/json/schema/other/version-verbose.json';

    final public const PATH_JSON_VERSION_SCHEMA = 'data/json/schema/other/version-verbose.schema.json';
}
