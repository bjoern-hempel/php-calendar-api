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

use App\Utils\GPSConverter;
use Exception;

/**
 * Class GPSPosition
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-22)
 * @package App\DataType
 */
class GPSPosition
{
    /** @var array<string, int|float|string|null> */
    protected array $data;

    public const SECONDS_PER_HOUR = 3600;

    public const FORMAT_DMS_SHORT_1 = '%d°%d′%s″%s';

    public const FORMAT_DMS_SHORT_2 = '%s%d°%d′%s″';

    /**
     * GPSPosition constructor.
     *
     * @param array<string, int|float|string> $data
     * @param string|null $direction
     * @throws Exception
     */
    public function __construct(array $data, ?string $direction = null)
    {
        assert(array_key_exists('degree', $data));
        assert(array_key_exists('minutes', $data));
        assert(array_key_exists('seconds', $data));

        if ($direction !== null) {
            $data['direction'] = $direction;
        }

        if (array_key_exists('direction', $data)) {
            $data['type'] = GPSConverter::getType(strval($data['direction']));
        } else {
            $data = array_merge(
                $data,
                [
                    'type' => null,
                    'direction' => null,
                ]
            );
        }

        $this->data = $data;
    }

    /**
     * Returns the data object.
     *
     * @return array<string, int|float|string|null>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the degree of gps position.
     *
     * @return int
     */
    public function getDegree(): int
    {
        return intval($this->data['degree']);
    }

    /**
     * Returns the minutes of gps position.
     *
     * @return int
     */
    public function getMinutes(): int
    {
        return intval($this->data['minutes']);
    }

    /**
     * Returns the minutes of gps position.
     *
     * @return float
     */
    public function getSeconds(): float
    {
        return floatval($this->data['seconds']);
    }

    /**
     * Returns the direction of gps position.
     *
     * @return string|null
     */
    public function getDirection(): ?string
    {
        if (is_null($this->data['direction'])) {
            return null;
        }

        return strval($this->data['direction']);
    }

    /**
     * Returns the type of gps position.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        if (is_null($this->data['type'])) {
            return null;
        }

        return strval($this->data['type']);
    }

    /**
     * Returns dms of gps position.
     *
     * @param string $format
     * @return string
     * @throws Exception
     */
    public function getDms(string $format = self::FORMAT_DMS_SHORT_1): string
    {
        return match ($format) {
            GPSPosition::FORMAT_DMS_SHORT_1 => sprintf($format, $this->getDegree(), $this->getMinutes(), $this->getSeconds(), $this->getDirection()),
            GPSPosition::FORMAT_DMS_SHORT_2 => sprintf($format, $this->getDirection(), $this->getDegree(), $this->getMinutes(), $this->getSeconds()),
            default => throw new Exception(sprintf('Unknown format "%s" given (%s:%d).', $format, __FILE__, __LINE__)),
        };
    }

    /**
     * Return decimal degree.
     *
     * @return float
     */
    public function getDecimalDegree(): float
    {
        $multiplication = in_array($this->getDirection(), [GPSConverter::DIRECTION_SOUTH, GPSConverter::DIRECTION_WEST]) ? -1 : 1;

        return $multiplication * round($this->getDegree() + ((($this->getMinutes() * 60) + $this->getSeconds()) / self::SECONDS_PER_HOUR), 6);
    }
}
