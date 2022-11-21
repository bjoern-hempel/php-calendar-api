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

use CrEOF\Spatial\PHP\Types\Geometry\Point as SpatialPoint;

/**
 * Class Point
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-28)
 * @package App\DataType
 */
class Point extends SpatialPoint
{
    /**
     * Gets the latitude (!) direction. CrEOF\Spatial\PHP\Types\Geometry\Point uses longitude as latitude.
     *
     * @return string
     */
    public function getLongitudeDirection(): string
    {
        return $this->getX() < 0 ? Coordinate::DIRECTION_SOUTH : Coordinate::DIRECTION_NORTH;
    }

    /**
     * Gets the longitude (!) direction. CrEOF\Spatial\PHP\Types\Geometry\Point uses latitude as longitude.
     *
     * @return string
     */
    public function getLatitudeDirection(): string
    {
        return $this->getY() < 0 ? Coordinate::DIRECTION_WEST : Coordinate::DIRECTION_EAST;
    }
}
