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

namespace App\DataType;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Coordinate
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-22)
 * @package App\DataType
 */
class Coordinate
{
    /* X Place */
    protected GPSPosition $longitude;

    /* Y Place */
    protected GPSPosition $latitude;

    public const TYPE_LATITUDE = 'latitude';

    public const TYPE_LONGITUDE = 'longitude';

    public const DIRECTION_NORTH = 'N';

    public const DIRECTION_SOUTH = 'S';

    public const DIRECTION_WEST = 'W';

    public const DIRECTION_EAST = 'E';

    public const UNIT_LATITUDE = [self::DIRECTION_NORTH, self::DIRECTION_SOUTH, ];

    public const UNIT_LONGITUDE = [self::DIRECTION_WEST, self::DIRECTION_EAST, ];

    /**
     * Place constructor.
     *
     * @param GPSPosition $longitude
     * @param GPSPosition $latitude
     */
    public function __construct(GPSPosition $longitude, GPSPosition $latitude)
    {
        assert($longitude->getType() === self::TYPE_LONGITUDE);
        assert($latitude->getType() === self::TYPE_LATITUDE);

        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    /**
     * Returns the longitude position.
     *
     * @return GPSPosition
     */
    public function getLongitude(): GPSPosition
    {
        return $this->longitude;
    }

    /**
     * Returns the latitude position.
     *
     * @return GPSPosition
     */
    public function getLatitude(): GPSPosition
    {
        return $this->latitude;
    }

    /**
     * Returns the position of longitude and latitude (float).
     *
     * @return float[]
     */
    #[ArrayShape(['longitude' => "float", 'latitude' => "float"])]
    public function getDecimalDegree(): array
    {
        return [
            'longitude' => $this->getLongitude()->getDecimalDegree(),
            'latitude' => $this->getLatitude()->getDecimalDegree(),
        ];
    }

    /**
     * Returns the position of longitude and latitude (string).
     *
     * @param string $format
     * @return string[]
     * @throws Exception
     */
    #[ArrayShape(['longitude' => "string", 'latitude' => "string"])]
    public function getDms(string $format = GPSPosition::FORMAT_DMS_SHORT_1): array
    {
        return [
            'longitude' => $this->getLongitude()->getDms($format),
            'latitude' => $this->getLatitude()->getDms($format),
        ];
    }

    /**
     * Returns the google string of longitude and latitude (string).
     *
     * @return string
     * @throws Exception
     */
    public function getGoogle(): string
    {
        return sprintf('https://www.google.de/maps/place/%s+%s', $this->getLongitude()->getDms(), $this->getLatitude()->getDms());
    }
}
