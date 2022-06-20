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

use App\Constant\Code;
use App\Entity\Place;
use App\Entity\PlaceL;
use App\Entity\PlaceP;
use App\Entity\PlaceS;
use App\Entity\PlaceT;
use App\Entity\PlaceV;
use App\Service\Entity\PlaceLoaderService;
use App\Utils\GPSConverter;
use App\Utils\Timer;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use function PHPUnit\Framework\isNan;

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

    protected bool $verbose = false;

    public const KEY_NAME_FORMAT = 'format';
    public const KEY_NAME_TITLE = 'title';
    public const KEY_NAME_UNIT = 'unit';
    public const KEY_NAME_UNIT_BEFORE = 'unit-before';
    public const KEY_NAME_VALUE = 'value';
    public const KEY_NAME_VALUE_FORMATTED = 'value-formatted';

    public const KEY_NAME_PLACE_LATITUDE = 'place-latitude';
    public const KEY_NAME_PLACE_LONGITUDE = 'place-longitude';
    public const KEY_NAME_PLACE_LATITUDE_DMS = 'place-latitude-dms';
    public const KEY_NAME_PLACE_LONGITUDE_DMS = 'place-longitude-dms';
    public const KEY_NAME_PLACE_POINT = 'place-point';
    public const KEY_NAME_PLACE_GOOGLE_LINK = 'place-google-link';
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
    public const KEY_NAME_PLACE_DISTANCE_DB = 'place-distance-db';
    public const KEY_NAME_PLACE_DISTANCE_METER = 'place-distance-meter';
    public const KEY_NAME_PLACE_DEM = 'place-dem';
    public const KEY_NAME_PLACE_ADMIN1 = 'place-admin1';
    public const KEY_NAME_PLACE_ADMIN2 = 'place-admin2';
    public const KEY_NAME_PLACE_ADMIN3 = 'place-admin3';
    public const KEY_NAME_PLACE_ADMIN4 = 'place-admin4';
    public const KEY_NAME_CITY_OR_RURAL = 'city-or-rural';
    public const KEY_NAME_PLACE_TIME_TAKEN = 'time-taken';

    public const WIDTH_TITLE = 30;

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

        $this->placeLoaderService->setDebug($debug);

        return $this;
    }

    /**
     * Sets verbose mode.
     *
     * @param bool $verbose
     * @return $this
     */
    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;

        $this->placeLoaderService->setVerbose($verbose);

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
        $formatted = sprintf($format, strval($value));

        $data = [
            self::KEY_NAME_TITLE => $title,
            self::KEY_NAME_FORMAT => $format,
            self::KEY_NAME_UNIT => $unit,
            self::KEY_NAME_UNIT_BEFORE => $unitBefore,
            self::KEY_NAME_VALUE => $value,
            self::KEY_NAME_VALUE_FORMATTED => sprintf('%s%s%s', $unitBefore, $valueFormatted !== null ? $valueFormatted : $formatted, $unit),
        ];

        if ($addValues !== null) {
            $data = array_merge($data, $addValues);
        }

        return $data;
    }

    /**
     * Gets location place.
     *
     * @param float $latitude
     * @param float $longitude
     * @param array<string, Place[]> $data
     * @return PlaceP|null
     * @throws DoctrineDBALException
     * @throws NonUniqueResultException
     */
    public function getLocationPlace(float $latitude, float $longitude, array &$data = []): ?PlaceP
    {
        return $this->placeLoaderService->findPlacePByPosition($latitude, $longitude, Code::FEATURE_CODES_P_ADMIN_PLACES, $data);
    }

    /**
     * Finds first location by name.
     *
     * @param string $name
     * @return Place|null
     * @throws Exception
     */
    public function getLocationByName(string $name): ?Place
    {
        $places = $this->placeLoaderService->findByName($name);

        if (count($places) <= 0) {
            return null;
        }

        return $places[0];
    }

    /**
     * Finds all locations by name.
     *
     * @param string $name
     * @return Place[]
     * @throws Exception
     */
    public function getLocationsByName(string $name): array
    {
        return $this->placeLoaderService->findByName($name);
    }

    /**
     * Finds first location by code:id.
     *
     * @param string $codeId
     * @return Place|null
     * @throws Exception
     */
    public function getLocationByCodeId(string $codeId): ?Place
    {
        return $this->placeLoaderService->findByCodeId($codeId);
    }

    /**
     * Sets place informations.
     *
     * @param array<string, array<string, mixed>> $dataReturn
     * @param Place $place
     * @param bool $addDistance
     * @return void
     */
    public function setPlaceInformation(array &$dataReturn, Place $place, bool $addDistance = false): void
    {
        $dataReturn = array_merge($dataReturn, [
            self::KEY_NAME_PLACE_COUNTRY => $this->getData('Place Country (translated)', $place->getCountry(), '%s', null),
            self::KEY_NAME_PLACE_COUNTRY_CODE => $this->getData('Place Country Code', $place->getCountryCode(), '%s', null),
            self::KEY_NAME_PLACE_TIMEZONE => $this->getData('Place Timezone', $place->getTimezone(), '%s', null),
            self::KEY_NAME_PLACE_POPULATION => $this->getData('Place Population', $place->getPopulation(), '%s', null),
            self::KEY_NAME_PLACE_ELEVATION => $this->getData('Place Elevation', $place->getElevation(), '%s', ' m'),
            self::KEY_NAME_PLACE_FEATURE_CLASS => $this->getData('Place Feature Class', $place->getFeatureClass(), '%s', null),
            self::KEY_NAME_PLACE_FEATURE_CODE => $this->getData('Place Feature Code', $place->getFeatureCode(), '%s', null),
            self::KEY_NAME_PLACE_DEM => $this->getData('Digital Elevation Model', $place->getDem(), '%s', ' m'),
            self::KEY_NAME_PLACE_ADMIN1 => $this->getData('Admin1 Code', $place->getAdmin1Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN2 => $this->getData('Admin2 Code', $place->getAdmin2Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN3 => $this->getData('Admin3 Code', $place->getAdmin3Code(), '%s', null),
            self::KEY_NAME_PLACE_ADMIN4 => $this->getData('Admin4 Code', $place->getAdmin4Code(), '%s', null),
            self::KEY_NAME_CITY_OR_RURAL => $this->getData('City or rural', $place->isCity() ? 'Stadt' : 'Ländliche Gegend', '%s', null),
        ]);

        if ($addDistance) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_DISTANCE_DB => $this->getData('Place Distance DB', sprintf('%.6f', $place->getDistanceDb()), '%s', null),
                self::KEY_NAME_PLACE_DISTANCE_METER => $this->getData('Place Distance Meters', sprintf('%.2f', $place->getDistanceMeter()), '%.2f', ' m'),
            ]);
        }
    }

    /**
     * Gets full location data.
     *
     * @param float $latitude
     * @param float $longitude
     * @param array<string, Place[]> $data
     * @param Place|null $placeSource
     * @return array<string, array<string, mixed>>
     * @throws DoctrineDBALException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getLocationDataFull(float $latitude, float $longitude, array &$data = [], ?Place $placeSource = null): array
    {
        $timer = Timer::start();
        $place = $this->placeLoaderService->findPlacePByPosition($latitude, $longitude, Code::FEATURE_CODES_P_ADMIN_PLACES, $data, $placeSource);
        $time = Timer::stop($timer);

        if ($place === null) {
            return [];
        }

        $dataReturn = [
            self::KEY_NAME_PLACE_LATITUDE => $this->getData('Latitude', $latitude, '%.5f', '°'),
            self::KEY_NAME_PLACE_LONGITUDE => $this->getData('Longitude', $longitude, '%.5f', '°'),
            self::KEY_NAME_PLACE_LATITUDE_DMS => $this->getData('Latitude DMS', GPSConverter::decimalDegree2dms($latitude, $latitude < 0 ? GPSConverter::DIRECTION_SOUTH : GPSConverter::DIRECTION_NORTH), '%s', null),
            self::KEY_NAME_PLACE_LONGITUDE_DMS => $this->getData('Longitude DMS', GPSConverter::decimalDegree2dms($longitude, $longitude < 0 ? GPSConverter::DIRECTION_WEST : GPSConverter::DIRECTION_EAST), '%s', null),
            self::KEY_NAME_PLACE_POINT => $this->getData('Location Point', sprintf('POINT(%.5f %.5f)', $latitude, $longitude), '%s', null),
            self::KEY_NAME_PLACE_GOOGLE_LINK => $this->getData('Google Link', GPSConverter::decimalDegree2google($latitude, $longitude, $latitude < 0 ? GPSConverter::DIRECTION_SOUTH : GPSConverter::DIRECTION_NORTH, $longitude < 0 ? GPSConverter::DIRECTION_WEST : GPSConverter::DIRECTION_EAST), '%s', null),
        ];

        /* PlaceP */
        if ($place->getDistrict() !== null) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_DISTRICT => $this->getData('District', $place->getDistrict()->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceA */
        if ($place->getCity() !== null) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_CITY => $this->getData('City', $place->getCity()->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceA */
        if ($place->getState() !== null) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_STATE => $this->getData('Place State', $place->getState()->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceL */
        $firstPark = $place->getFirstPark(true, $placeSource);
        if (!is_null($firstPark)) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_PARK => $this->getData('Place Park', $firstPark->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceT */
        $firstMountain = $place->getFirstMountain(true, $placeSource);
        if (!is_null($firstMountain)) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_MOUNTAIN => $this->getData('Place Mountain', $firstMountain->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceS */
        $firstSpot = $place->getFirstSpot(true, $placeSource);
        if (!is_null($firstSpot)) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_SPOT => $this->getData('Place Spot', $firstSpot->getName($this->verbose), '%s', null),
            ]);
        }

        /* PlaceV */
        $firstForest = $place->getFirstForest(true, $placeSource);
        if (!is_null($firstForest)) {
            $dataReturn = array_merge($dataReturn, [
                self::KEY_NAME_PLACE_FOREST => $this->getData('Place Forest', $firstForest->getName($this->verbose), '%s', null),
            ]);
        }

        $dataReturn = array_merge($dataReturn, [
            self::KEY_NAME_PLACE_FULL => $this->getData('Place Full', $place->getNameFull($this->verbose, $placeSource, true), '%s', null),
        ]);

        if ($placeSource !== null) {

            /* Set country to place source. */
            if ($place->getCountry() !== null) {
                $placeSource->setCountry($place->getCountry());
            }

            $this->setPlaceInformation($dataReturn, $placeSource);
        } else {
            $this->setPlaceInformation($dataReturn, $place, true);
        }

        return array_merge($dataReturn, [
            self::KEY_NAME_PLACE_TIME_TAKEN => $this->getData('Time', $time, '%.3f', ' s'),
        ]);
    }

    /**
     * Gets location data.
     *
     * @param float $latitude
     * @param float $longitude
     * @param array<string, Place[]> $data
     * @param Place|null $placeSource
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getLocationDataFormatted(float $latitude, float $longitude, array &$data = [], ?Place $placeSource = null): array
    {
        $locationData = $this->getLocationDataFull($latitude, $longitude, $data, $placeSource);

        $array = [];

        foreach ($locationData as $key => $value) {
            $array[$key] = $value[self::KEY_NAME_VALUE_FORMATTED];
        }

        return $array;
    }

    /**
     * Gets location data.
     *
     * @param float $latitude
     * @param float $longitude
     * @param array<string, Place[]> $data
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getLocationData(float $latitude, float $longitude, array &$data = []): array
    {
        $locationData = $this->getLocationDataFull($latitude, $longitude, $data);

        $array = [];

        foreach ($locationData as $key => $value) {
            $array[$key] = $value['value'];
        }

        return $array;
    }

    /**
     * Calculate the distance between two points.
     *
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     * @param int|null $decimals
     * @return array<string, float>
     */
    public static function getDistanceBetweenTwoPoints(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo, ?int $decimals = null): array
    {
        $theta = $longitudeFrom - $longitudeTo;

        /* Calculate distance. */
        if ($latitudeFrom === $latitudeTo && $longitudeFrom === $longitudeTo) {
            $distance = 0;
        } else {
            $distance = (sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo))) + (cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta)));
            $distance = acos($distance);
            $distance = rad2deg($distance);
        }

        /* Convert distances. */
        $miles = $distance * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;

        if ($decimals !== null) {
            $miles = round($miles, $decimals);
            $feet = round($feet, $decimals);
            $yards = round($yards, $decimals);
            $kilometers = round($kilometers, $decimals);
            $meters = round($meters, $decimals);
        }

        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }

    /**
     * Calculate the distance between two points in meters.
     *
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     * @param int|null $decimals
     * @return float
     */
    public static function getDistanceBetweenTwoPointsInMeter(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo, ?int $decimals = null): float
    {
        $distance = self::getDistanceBetweenTwoPoints($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $decimals);

        return $distance['meters'];
    }
}
