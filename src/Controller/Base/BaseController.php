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

namespace App\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Abstract class BaseController
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-19)
 * @package App\Controller
 */
abstract class BaseController extends AbstractController
{
    public const KEY_NAME_ENCODED = 'encoded';

    public const ROUTE_NAME_APP_CALENDAR_INDEX = 'app_calendar_index';

    public const ROUTE_NAME_APP_CALENDAR_INDEX_ENCODED = 'app_calendar_index_encoded';

    public const CONFIG_APP_CALENDAR_INDEX = [
        'path' => 'calendar',
        'parameter' => [
            'hash' => 'string',
            'userId' => 'integer',
            'calendarId' => 'integer',
        ],
        'parameterEncoded' => [
            self::KEY_NAME_ENCODED => 'string',
        ]
    ];

    public const ROUTE_NAME_APP_CALENDAR_DETAIL = 'app_calendar_detail';

    public const ROUTE_NAME_APP_CALENDAR_DETAIL_ENCODED = 'app_calendar_detail_encoded';

    public const CONFIG_APP_CALENDAR_DETAIL = [
        'path' => 'calendar/detail',
        'parameter' => [
            'hash' => 'string',
            'userId' => 'integer',
            'calendarImageId' => 'integer',
        ],
        'parameterEncoded' => [
            self::KEY_NAME_ENCODED => 'string',
        ]
    ];
}
