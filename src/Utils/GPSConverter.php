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

namespace App\Utils;

use App\DataType\Coordinate;
use App\DataType\GPSPosition;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class GPSConverter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-22)
 * @package App\Command
 */
class GPSConverter
{
    public const UNIT_DEGREE = [
        '°',
    ];

    public const UNIT_MINUTES = [
        '’',
        '\'',
        '′',
    ];

    public const UNIT_SECONDS = [
        '\'’',
        '"',
        '″',
    ];

    protected const REGEXP_VERSION_1 = 1;

    protected const REGEXP_VERSION_2 = 2;

    public const DIRECTION_NORTH = 'N';
    public const DIRECTION_SOUTH = 'S';
    public const DIRECTION_WEST = 'W';
    public const DIRECTION_EAST = 'E';

    /**
     * @param array<int, string> $units
     * @param int $version
     * @return string
     * @throws Exception
     */
    protected static function getRegexp(array $units, int $version): string
    {
        return match ($version) {
            self::REGEXP_VERSION_1 => sprintf(
                '~^([0-9]+)(?:%s)[ ]*([0-9]+)(?:%s)[ ]*([0-9]+(?:\.[0-9]+)?)(?:%s)[ ]*(%s)$~',
                implode('|', self::UNIT_DEGREE),
                implode('|', self::UNIT_MINUTES),
                implode('|', self::UNIT_SECONDS),
                implode('|', $units)
            ),
            self::REGEXP_VERSION_2 => sprintf(
                '~^(%s)[ ]*([0-9]+)(?:%s)[ ]*([0-9]+)(?:%s)[ ]*([0-9]+(?:\.[0-9]+)?)(?:%s)$~',
                implode('|', $units),
                implode('|', self::UNIT_DEGREE),
                implode('|', self::UNIT_MINUTES),
                implode('|', self::UNIT_SECONDS)
            ),
            default => throw new Exception(sprintf('Unknown version "%d" given (%s:%d).', $version, __FILE__, __LINE__))
        };
    }

    /**
     * Returns array from given values.
     *
     * @param int $degree
     * @param int $minutes
     * @param float $seconds
     * @param string|null $direction
     * @return array<string, int|float|string>
     * @throws Exception
     */
    #[ArrayShape(['degree' => "int", 'minutes' => "int", 'seconds' => "float", 'direction' => "string", 'type' => "string"])]
    protected static function getData(int $degree, int $minutes, float $seconds, ?string $direction = null): array
    {
        $data = [
            'degree' => $degree,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];

        if ($direction !== null) {
            $data = array_merge(
                $data,
                [
                    'type' => self::getType($direction),
                    'direction' => $direction,
                ]
            );
        }

        return $data;
    }

    /**
     * Get type from direction.
     *
     * @param string $direction
     * @return string
     * @throws Exception
     */
    public static function getType(string $direction): string
    {
        return match ($direction) {
            Coordinate::DIRECTION_SOUTH, Coordinate::DIRECTION_NORTH => Coordinate::TYPE_LATITUDE,
            Coordinate::DIRECTION_EAST, Coordinate::DIRECTION_WEST => Coordinate::TYPE_LONGITUDE,
            default => throw new Exception(sprintf('Unknown direction "%s" given (%s:%d).', $direction, __FILE__, __LINE__)),
        };
    }

    /**
     * Parses given dms string.
     *
     * @param string $dms
     * @return array<string, int|float|string>
     * @throws Exception
     */
    #[ArrayShape(['degree' => "int", 'minutes' => "int", 'seconds' => "float", 'type' => "string", 'direction' => "string", ])]
    public static function parseDms(string $dms): array
    {
        foreach ([Coordinate::TYPE_LATITUDE => Coordinate::UNIT_LATITUDE, Coordinate::TYPE_LONGITUDE => Coordinate::UNIT_LONGITUDE] as $type => $units) {
            $matches = [];

            if (preg_match(self::getRegexp($units, self::REGEXP_VERSION_1), $dms, $matches)) {
                list(, $degree, $minutes, $seconds, $direction) = $matches;
                return self::getData(intval($degree), intval($minutes), floatval($seconds), $direction);
            }

            if (preg_match(self::getRegexp($units, self::REGEXP_VERSION_2), $dms, $matches)) {
                list(, $direction, $degree, $minutes, $seconds) = $matches;
                return self::getData(intval($degree), intval($minutes), floatval($seconds), $direction);
            }
        }

        throw new Exception(sprintf('Unable to parse dms string (%s:%d).', __FILE__, __LINE__));
    }

    /**
     * Converts given decimal degree into dms string.
     *
     * @param float $decimalDegree
     * @param string|null $direction
     * @return array<string, int|float|string>
     * @throws Exception
     */
    #[ArrayShape(['degree' => "int", 'minutes' => "int", 'seconds' => "float"])]
    public static function parseDecimalDegree(float $decimalDegree, ?string $direction = null): array
    {
        $degree = floor($decimalDegree);

        $secondsOverall = ($decimalDegree - $degree) * GPSPosition::SECONDS_PER_HOUR;

        $minutes = floor($secondsOverall / 60);

        $seconds = $secondsOverall - $minutes * 60;

        $data = [
            'degree' => intval($degree),
            'minutes' => intval($minutes),
            'seconds' => round(floatval($seconds), 6),
            'decimal' => $decimalDegree,
        ];

        if ($direction !== null) {
            $data = array_merge(
                $data,
                [
                    'type' => self::getType($direction),
                    'direction' => $direction,
                ]
            );
        }

        return $data;
    }

    /**
     * Converts given dms string into decimal degree.
     *
     * @param string $dms
     * @param string|null $direction
     * @return float
     * @throws Exception
     */
    public static function dms2DecimalDegree(string $dms, ?string $direction = null): float
    {
        $value = (new GPSPosition(self::parseDms($dms)))->getDecimalDegree();

        if (in_array($direction, [self::DIRECTION_WEST, self::DIRECTION_SOUTH])) {
            $value *= -1;
        }

        return $value;
    }

    /**
     * Converts given dms string into direction.
     *
     * @param string $dms
     * @return string|null
     * @throws Exception
     */
    public static function dms2Direction(string $dms): ?string
    {
        return (new GPSPosition(self::parseDms($dms)))->getDirection();
    }

    /**
     * Converts given dms coordinates string into decimal degree.
     *
     * @param string $dmsLongitude
     * @param string $dmsLatitude
     * @return float[]
     * @throws Exception
     */
    #[ArrayShape(['longitude' => "float", 'latitude' => "float"])]
    public static function dms2DecimalDegrees(string $dmsLongitude, string $dmsLatitude): array
    {
        return (new Coordinate(new GPSPosition(self::parseDms($dmsLongitude)), new GPSPosition(self::parseDms($dmsLatitude))))->getDecimalDegree();
    }

    /**
     * Converts given decimal degree into dms.
     *
     * @param float $decimalDegree
     * @param string|null $direction
     * @param string $format
     * @return string
     * @throws Exception
     */
    public static function decimalDegree2dms(float $decimalDegree, ?string $direction = null, string $format = GPSPosition::FORMAT_DMS_SHORT_1): string
    {
        if ($direction !== null) {
            $decimalDegree = $decimalDegree < 0 ? -$decimalDegree : $decimalDegree;
        }

        return (new GPSPosition(self::parseDecimalDegree($decimalDegree), $direction))->getDms($format);
    }

    /**
     * Converts given decimal degree into dms.
     *
     * @param float $decimalDegreeLongitude
     * @param float $decimalDegreeLatitude
     * @param string|null $directionLongitude
     * @param string|null $directionLatitude
     * @param string $format
     * @return string[]
     * @throws Exception
     */
    #[ArrayShape(['longitude' => "string", 'latitude' => "string"])]
    public static function decimalDegree2dmss(float $decimalDegreeLongitude, float $decimalDegreeLatitude, ?string $directionLongitude = null, ?string $directionLatitude = null, string $format = GPSPosition::FORMAT_DMS_SHORT_1): array
    {
        return (new Coordinate(new GPSPosition(self::parseDecimalDegree($decimalDegreeLongitude, $directionLongitude)), new GPSPosition(self::parseDecimalDegree($decimalDegreeLatitude, $directionLatitude))))->getDms($format);
    }

    /**
     * Converts given decimal degree into dms.
     *
     * @param float $decimalDegreeLongitude
     * @param float $decimalDegreeLatitude
     * @param string|null $directionLongitude
     * @param string|null $directionLatitude
     * @return string
     * @throws Exception
     */
    #[ArrayShape(['longitude' => "string", 'latitude' => "string"])]
    public static function decimalDegree2google(float $decimalDegreeLongitude, float $decimalDegreeLatitude, ?string $directionLongitude = null, ?string $directionLatitude = null): string
    {
        if ($directionLatitude !== null) {
            $decimalDegreeLongitude = $decimalDegreeLongitude < 0 ? -$decimalDegreeLongitude : $decimalDegreeLongitude;
        }

        if ($directionLatitude !== null) {
            $decimalDegreeLatitude = $decimalDegreeLatitude < 0 ? -$decimalDegreeLatitude : $decimalDegreeLatitude;
        }

        return (new Coordinate(
            new GPSPosition(self::parseDecimalDegree($decimalDegreeLongitude, $directionLongitude)),
            new GPSPosition(self::parseDecimalDegree($decimalDegreeLatitude, $directionLatitude))
        ))->getGoogle();
    }

    /**
     * Parse full location string and converts it to a float array.
     *
     * Allowed formats:
     * ----------------
     * • '47.900635 13.601868'
     * • '47.900635, 13.601868'
     * • '47.900635,13.601868'
     * • '47.900635°,13.601868°'
     * • '47.900635° -13.601868°'
     * • '47.900635°, -13.601868°'
     * • '47.900635°,-13.601868°'
     * • '47.900635°,_13.601868°'
     * • '47°54′2.286″E 13°36′6.7248″N'
     * • 'E47°54′2.286″ N13°36′6.7248″'
     * • etc.
     *
     * @param string $fullLocation
     * @return float[]
     * @throws Exception
     */
    public static function parseFullLocation2DecimalDegrees(string $fullLocation): array
    {
        $matches = [];
        switch (true) {
            /* Google Link https://maps.app.goo.gl/PHq5axBaDdgRWj4T6 */
            case preg_match('~(https://maps.app.goo.gl/[a-zA-Z0-9]+)$~', $fullLocation, $matches):
                list($latitude, $longitude) = self::parseLatitudeAndLongitudeFromGoogleLink($matches[1]);
                break;

            default:
                list($latitude, $longitude) = self::parseLatitudeAndLongitudeFromString($fullLocation);
        }

        return [$latitude, $longitude];
    }

    /**
     * Parses Google Link (name; direct; without redirect).
     *
     * @param string $googleLink
     * @return string|false
     * @throws Exception
     */
    public static function parseNameFromGoogleLinkDirect(string $googleLink): string|false
    {
        $matchesLocation = [];
        if (!preg_match('~/maps/place/([^/]+)/~', $googleLink, $matchesLocation)) {
            return false;
        }

        $name = urldecode($matchesLocation[1]);

        return str_replace('+', ' ', $name);
    }

    /**
     * Parses Google Link (latitude; longitude; direct; without redirect).
     *
     * @param string $googleLink
     * @return float[]|false
     * @throws Exception
     */
    public static function parseLatitudeAndLongitudeFromGoogleLinkDirect(string $googleLink): array|false
    {
        $matchesLocation = [];
        if (!preg_match('~!3d([0-9]+\.[0-9]+)+.+!4d([0-9]+\.[0-9]+)~', $googleLink, $matchesLocation)) {
            return false;
        }
        list(, $latitude, $longitude) = $matchesLocation;

        $latitude = floatval($latitude);
        $longitude = floatval($longitude);

        return [$latitude, $longitude];
    }

    /**
     * Parses Google Link.
     *
     * @param string $googleLink
     * @return float[]
     * @throws Exception
     */
    protected static function parseLatitudeAndLongitudeFromGoogleLink(string $googleLink): array
    {
        $curl = curl_init($googleLink);

        if ($curl === false) {
            throw new Exception(sprintf('Unable to initiate curl (%s:%d).', __FILE__, __LINE__));
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $response = curl_exec($curl);

        if (is_bool($response)) {
            throw new Exception(sprintf('Unable to exec curl (%s:%d).', __FILE__, __LINE__));
        }

        $header_size = intval(curl_getinfo($curl, CURLINFO_HEADER_SIZE));

        $headerLines = substr($response, 0, $header_size);

        // location: https://www.google.com/maps/place/Malbork,+Polen/data=!4m6!3m5!1s0x46fd5bffa9b675d5:0xb4e2fe366cccb936!7e2!8m2!3d54.073048299999996!4d18.992402?utm_source=mstt_1&entry=gps
        $matchesLocation = [];
        if (preg_match('~^location: .+!3d([0-9]+\.[0-9]+)+.+!4d([0-9]+\.[0-9]+).+~m', $headerLines, $matchesLocation)) {
            list(, $latitude, $longitude) = $matchesLocation;

            $latitude = floatval($latitude);
            $longitude = floatval($longitude);
        } else {
            $latitude = 47.900635;
            $longitude = 13.601868;
        }

        return [$latitude, $longitude];
    }

    /**
     * Parse from full location string.
     *
     * @param string $fullLocation
     * @return float[]
     * @throws Exception
     */
    protected static function parseLatitudeAndLongitudeFromString(string $fullLocation): array
    {
        $split = preg_split('~[, ]+~', $fullLocation);

        if ($split === false) {
            throw new Exception(sprintf('Unable to split given full location string (%s:%d).', __FILE__, __LINE__));
        }

        list($latitude, $longitude) = $split;

        $latitude = trim(str_replace('_', '-', $latitude));
        $longitude = trim(str_replace('_', '-', $longitude));

        $numberRegexp = '~^-?\d+\.\d+[°]*~';

        if (preg_match($numberRegexp, $latitude)) {
            $latitude = floatval($latitude);
        } else {
            $latitude = self::dms2DecimalDegree($latitude);
        }

        if (preg_match($numberRegexp, $longitude)) {
            $longitude = floatval($longitude);
        } else {
            $longitude = self::dms2DecimalDegree($longitude);
        }

        return [$latitude, $longitude];
    }
}
