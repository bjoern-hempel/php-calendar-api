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

namespace App\Service;

use App\Service\Entity\PlaceLoaderService;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class LocationDataService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-23)
 * @package App\Service
 */
class LocationDataService
{
    protected PlaceLoaderService $placeLoaderService;

    protected bool $debug = false;

    public const KEY_NAME_FORMAT = 'format';
    public const KEY_NAME_TITLE = 'title';
    public const KEY_NAME_UNIT = 'unit';
    public const KEY_NAME_UNIT_BEFORE = 'unit-before';
    public const KEY_NAME_VALUE = 'value';
    public const KEY_NAME_VALUE_FORMATTED = 'value-formatted';

    public const KEY_NAME_PLACE = 'place';
    public const KEY_NAME_PLACE_FULL = 'place-full';
    public const KEY_NAME_PLACE_DISTRICT = 'place-district';
    public const KEY_NAME_PLACE_CITY = 'place-city';
    public const KEY_NAME_PLACE_STATE = 'place-state';
    public const KEY_NAME_PLACE_PARK = 'place-park';
    public const KEY_NAME_PLACE_MOUNTAIN = 'place-mountain';
    public const KEY_NAME_PLACE_SPOT = 'place-spot';
    public const KEY_NAME_PLACE_FOREST = 'place-forest';
    public const KEY_NAME_PLACE_COUNTRY = 'place-country';
    public const KEY_NAME_PLACE_COUNTRY_CODE = 'place-country-code';
    public const KEY_NAME_PLACE_TIMEZONE = 'place-timezone';
    public const KEY_NAME_PLACE_POPULATION = 'place-population';
    public const KEY_NAME_PLACE_ELEVATION = 'place-elevation';
    public const KEY_NAME_PLACE_FEATURE_CLASS = 'place-feature-class';
    public const KEY_NAME_PLACE_FEATURE_CODE = 'place-feature-code';
    public const KEY_NAME_PLACE_DISTANCE = 'place-distance';
    public const KEY_NAME_PLACE_DEM = 'place-dem';
    public const KEY_NAME_PLACE_ADMIN1 = 'place-admin1';
    public const KEY_NAME_PLACE_ADMIN2 = 'place-admin2';
    public const KEY_NAME_PLACE_ADMIN3 = 'place-admin3';
    public const KEY_NAME_PLACE_ADMIN4 = 'place-admin4';

    /**
     * LocationDataService constructor.
     *
     * @param PlaceLoaderService $placeLoaderService
     */
    public function __construct(PlaceLoaderService $placeLoaderService)
    {
        $this->placeLoaderService = $placeLoaderService;
    }

    /**
     * Sets debug mode.
     *
     * @param bool $debug
     * @return $this
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Returns a single data value.
     *
     * @param string $title
     * @param mixed $value
     * @param string $format
     * @param string|null $unit
     * @param string|null $unitBefore
     * @param string|null $valueFormatted
     * @param array<string, string|mixed|null> $addValues
     * @return array<string, string|mixed|null>
     */
    #[ArrayShape([self::KEY_NAME_TITLE => "string", self::KEY_NAME_FORMAT => "string", self::KEY_NAME_UNIT => "null|string", self::KEY_NAME_UNIT_BEFORE => "null|string", self::KEY_NAME_VALUE => "mixed", self::KEY_NAME_VALUE_FORMATTED => "string"])]
    protected function getData(string $title, mixed $value, string $format, ?string $unit, ?string $unitBefore = null, ?string $valueFormatted = null, array $addValues = null): array
    {
        $data = [
            self::KEY_NAME_TITLE => $title,
            self::KEY_NAME_FORMAT => $format,
            self::KEY_NAME_UNIT => $unit,
            self::KEY_NAME_UNIT_BEFORE => $unitBefore,
            self::KEY_NAME_VALUE => $value,
            self::KEY_NAME_VALUE_FORMATTED => sprintf('%s%s%s', $unitBefore, $valueFormatted !== null ? $valueFormatted : strval($value), $unit),
        ];

        if ($addValues !== null) {
            $data = array_merge($data, $addValues);
        }

        return $data;
    }

    /**
     * Gets full location data.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array<string, array<string, mixed>>
     * @throws DoctrineDBALException
     */
    public function getLocationDataFull(float $latitude, float $longitude): array
    {
        $place = $this->placeLoaderService->findPlacePByPosition($latitude, $longitude);

        if ($place === null) {
            return [];
        }

        $data = [
            self::KEY_NAME_PLACE => $this->getData('Place', $place->getName(), '%s', null),
        ];

        /* PlaceP */
        if ($place->getDistrict() !== null) {
            $data = array_merge($data, [
                self::KEY_NAME_PLACE_DISTRICT => $this->getData('District', $place->getDistrict()->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceA */
        if ($place->getCity() !== null) {
            $data = array_merge($data, [
                self::KEY_NAME_PLACE_CITY => $this->getData('City', $place->getCity()->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceA */
        if ($place->getState() !== null) {
            $data = array_merge($data, [
                self::KEY_NAME_PLACE_STATE => $this->getData('Place State', $place->getState()->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceL */
        if (count($place->getParks()) > 0) {
            $park = $place->getParks()[0];

            $data = array_merge($data, [
                self::KEY_NAME_PLACE_PARK => $this->getData('Place Park', $park->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceT */
        if (count($place->getMountains()) > 0) {
            $mountain = $place->getMountains()[0];

            $data = array_merge($data, [
                self::KEY_NAME_PLACE_MOUNTAIN => $this->getData('Place Mountain', $mountain->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceS */
        if (count($place->getSpots()) > 0) {
            $spot = $place->getSpots()[0];

            $data = array_merge($data, [
                self::KEY_NAME_PLACE_SPOT => $this->getData('Place Spot', $spot->getName($this->debug), '%s', null),
            ]);
        }

        /* PlaceV */
        if (count($place->getForests()) > 0) {
            $spot = $place->getForests()[0];

            $data = array_merge($data, [
                self::KEY_NAME_PLACE_FOREST => $this->getData('Place Forest', $spot->getName($this->debug), '%s', null),
            ]);
        }

        $data = array_merge($data, [
            self::KEY_NAME_PLACE_FULL => $this->getData('Place Full', $place->getNameFull($this->debug), '%s', null),
        ]);

        return array_merge($data, [
            self::KEY_NAME_PLACE_COUNTRY => $this->getData('Place Country (translated)', $place->getCountry(), '%s', null),
            self::KEY_NAME_PLACE_COUNTRY_CODE => $this->getData('Place Country Code', $place->getCountryCode(), '%s', null),
            self::KEY_NAME_PLACE_TIMEZONE => $this->getData('Place Timezone', $place->getTimezone(), '%s', null),
            self::KEY_NAME_PLACE_POPULATION => $this->getData('Place Population', $place->getPopulation(), '%s', null),
            self::KEY_NAME_PLACE_ELEVATION => $this->getData('Place Elevation', $place->getElevation(), '%s', ' m'),
            self::KEY_NAME_PLACE_FEATURE_CLASS => $this->getData('Place Feature Class', $place->getFeatureClass(), '%s', null),
            self::KEY_NAME_PLACE_FEATURE_CODE => $this->getData('Place Feature Code', $place->getFeatureCode(), '%s', null),
            self::KEY_NAME_PLACE_DISTANCE => $this->getData('Place Feature Code', $place->getDistance(), '%s', null),
            self::KEY_NAME_PLACE_DEM => $this->getData('Digital Elevation Model', $place->getDem(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN1 => $this->getData('Admin1 Code', $place->getAdmin1Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN2 => $this->getData('Admin2 Code', $place->getAdmin2Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN3 => $this->getData('Admin3 Code', $place->getAdmin3Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN4 => $this->getData('Admin4 Code', $place->getAdmin4Code(), '%s', null),
        ]);
    }

    /**
     * Gets location data.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getImageData(float $latitude, float $longitude): array
    {
        $locationData = $this->getLocationDataFull($latitude, $longitude);

        $array = [];

        foreach ($locationData as $key => $data) {
            $array[$key] = $data['value'];
        }

        return $array;
    }
}
