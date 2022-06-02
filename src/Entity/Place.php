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

namespace App\Entity;

use App\DataType\Point;
use App\Service\Entity\PlaceLoaderService;
use App\Utils\GPSConverter;
use CrEOF\Spatial\PHP\Types\Geometry\Point as CrEOFSpatialPHPTypesGeometryPoint;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Base entity superclass.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.0 (2022-05-21)
 * @package App\Entity\Base
 */
#[ORM\MappedSuperclass]
abstract class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'geoname_id', type: 'integer')]
    protected int $geonameId;

    #[ORM\Column(type: 'string', length: 1024)]
    protected string $name;

    #[ORM\Column(name: 'ascii_name', type: 'string', length: 1024)]
    protected string $asciiName;

    #[ORM\Column(name: 'alternate_names', type: 'string', length: 4096)]
    protected string $alternateNames;

    #[ORM\Column(type: 'point')]
    protected CrEOFSpatialPHPTypesGeometryPoint $coordinate;

    #[ORM\Column(name: 'feature_class', type: 'string', length: 1)]
    protected string $featureClass;

    #[ORM\Column(name: 'feature_code', type: 'string', length: 10)]
    protected string $featureCode;

    #[ORM\Column(name: 'country_code', type: 'string', length: 2)]
    protected string $countryCode;

    #[ORM\Column(type: 'string', length: 200)]
    protected string $cc2;

    #[ORM\Column(type: 'bigint', nullable: true)]
    protected ?string $population = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $elevation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $dem = null;

    #[ORM\Column(type: 'string', length: 40)]
    protected string $timezone;

    #[ORM\Column(name: 'modification_date', type: 'date')]
    protected DateTime $modificationDate;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    protected ?string $admin1Code = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    protected ?string $admin2Code = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    protected ?string $admin3Code = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    protected ?string $admin4Code = null;

    protected float $distanceDb = .0;

    protected float $distanceMeter = .0;

    protected ?PlaceP $cityP = null;

    protected ?PlaceA $cityA = null;

    protected PlaceA|PlaceP|null $district = null;

    protected PlaceA|PlaceP|null $city = null;

    protected ?PlaceA $state = null;

    /** @var PlaceL[] $parks */
    protected array $parks = [];

    /** @var PlaceP[] $places */
    protected array $places = [];

    /** @var PlaceS[] $spots */
    protected array $spots = [];

    /** @var PlaceT[] $mountains */
    protected array $mountains = [];

    /** @var PlaceV[] $forests */
    protected array $forests = [];

    protected ?string $country = null;

    protected ?string $populationAdmin = null;

    protected bool $isCity = false;

    protected string $templateAddName = '%s, %s';

    /**
     * Sets the id of this place.
     *
     * @param int $id
     * @return $this
     */
    public function setIdTmp(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the id of this place.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the geo name id of this place.
     *
     * @return int
     */
    public function getGeonameId(): int
    {
        return $this->geonameId;
    }

    /**
     * Sets the geo name id of this place.
     *
     * @param int $geonameId
     * @return $this
     */
    public function setGeonameId(int $geonameId): self
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    /**
     * Gets the name of this place.
     *
     * @param bool $withFeature
     * @return string
     */
    public function getName(bool $withFeature = false): string
    {
        /* Remove some special strings */
        $name = str_replace(
            [
                ', Kurort',
            ],
            '',
            $this->name
        );

        return $withFeature ? sprintf('%s (%s/%s)', $name, $this->getFeatureClass(), $this->getFeatureCode()) : $name;
    }

    /**
     * Gets the full name of this place.
     *
     * @param bool $detailed
     * @param Place|null $placeSource
     * @return string
     * @throws Exception
     */
    public function getNameFull(bool $detailed = false, ?Place $placeSource = null): string
    {
        $name = '';

        if ($this->getDistrict() !== null && !$this->strContains($name, $this->getDistrict()->getName())) {
            $name = sprintf($this->templateAddName, $name, $this->getDistrict()->getName($detailed));
        }

        if ($this->getCity() !== null && !$this->strContains($name, $this->getCity()->getName())) {
            $name = sprintf($this->templateAddName, $name, $this->getCity()->getName($detailed));
        }

        if ($this->getState() !== null && !$this->strContains($name, $this->getState()->getName())) {
            $name = sprintf($this->templateAddName, $name, $this->getState()->getName($detailed));
        }

        $name = sprintf($this->templateAddName, $name, $this->getCountry($detailed));

        $name = preg_replace('~^, ~', '', $name);

        if ($name === null) {
            throw new Exception(sprintf('Unable to replace comma (%s:%d).', __FILE__, __LINE__));
        }

        /* PlaceL */
        $firstPark = $this->getFirstPark(true, $placeSource);
        if (!is_null($firstPark) && !$this->strContains($name, $firstPark->getName())) {
            $name = sprintf($this->templateAddName, $firstPark->getName($detailed), $name);
        }

        /* PlaceT */
        $firstMountain = $this->getFirstMountain(true, $placeSource);
        if (!is_null($firstMountain) && !$this->strContains($name, $firstMountain->getName())) {
            $name = sprintf($this->templateAddName, $firstMountain->getName($detailed), $name);
        }

        /* PlaceS */
        $firstSpot = $this->getFirstSpot(true, $placeSource);
        if (!is_null($firstSpot) && !$this->strContains($name, $firstSpot->getName())) {
            $name = sprintf($this->templateAddName, $firstSpot->getName($detailed), $name);
        }

        /* PlaceV */
        $firstForest = $this->getFirstForest(true, $placeSource);
        if (!is_null($firstForest) && !$this->strContains($name, $firstForest->getName())) {
            $name = sprintf($this->templateAddName, $firstForest->getName($detailed), $name);
        }

        return $name;
    }

    /**
     * Sets the name of this place.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Adds a new name before the existing name of this place (Spot, Mountain, Area, etc.).
     *
     * @param string $addName
     * @return $this
     */
    public function addNameBefore(string $addName): self
    {
        if (!$this->strContains($this->getName(), $addName)) {
            $this->setName(sprintf($this->templateAddName, ucfirst($addName), $this->getName()));
        }

        return $this;
    }

    /**
     * Adds a new name after the existing name of this place (state, country, etc.).
     *
     * @param string $addName
     * @return $this
     */
    public function addNameAfter(string $addName): self
    {
        if (!$this->strContains($this->getName(), $addName)) {
            $this->setName(sprintf($this->templateAddName, $this->getName(), ucfirst($addName)));
        }

        return $this;
    }

    /**
     * Gets the ascii name of this place.
     *
     * @return string
     */
    public function getAsciiName(): string
    {
        return $this->asciiName;
    }

    /**
     * Sets the ascii name of this place.
     *
     * @param string $asciiName
     * @return $this
     */
    public function setAsciiName(string $asciiName): self
    {
        $this->asciiName = $asciiName;

        return $this;
    }

    /**
     * Gets the alternate name of this place.
     *
     * @return string
     */
    public function getAlternateNames(): string
    {
        return $this->alternateNames;
    }

    /**
     * Sets the alternate name of this place.
     *
     * @param string $alternateNames
     * @return $this
     */
    public function setAlternateNames(string $alternateNames): self
    {
        $this->alternateNames = $alternateNames;

        return $this;
    }

    /**
     * Gets the point of this place.
     *
     * @return CrEOFSpatialPHPTypesGeometryPoint
     */
    public function getCoordinate(): CrEOFSpatialPHPTypesGeometryPoint
    {
        return $this->coordinate;
    }

    /**
     * Sets the point of this place.
     *
     * @param CrEOFSpatialPHPTypesGeometryPoint $coordinate
     * @return $this
     */
    public function setCoordinate(CrEOFSpatialPHPTypesGeometryPoint $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    /**
     * Gets the feature class of this place.
     *
     * @return string
     */
    public function getFeatureClass(): string
    {
        return $this->featureClass;
    }

    /**
     * Sets the feature class of this place.
     *
     * @param string $featureClass
     * @return $this
     */
    public function setFeatureClass(string $featureClass): self
    {
        $this->featureClass = $featureClass;

        return $this;
    }

    /**
     * Gets the feature code of this place.
     *
     * @return string
     */
    public function getFeatureCode(): string
    {
        return $this->featureCode;
    }

    /**
     * Sets the feature code of this place.
     *
     * @param string $featureCode
     * @return $this
     */
    public function setFeatureCode(string $featureCode): self
    {
        $this->featureCode = $featureCode;

        return $this;
    }

    /**
     * Gets the country code of this place.
     *
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Sets the country code of this place.
     *
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Gets the cc2 of this place.
     *
     * @return string
     */
    public function getCc2(): string
    {
        return $this->cc2;
    }

    /**
     * Sets the cc2 of this place.
     *
     * @param string $cc2
     * @return $this
     */
    public function setCc2(string $cc2): self
    {
        $this->cc2 = $cc2;

        return $this;
    }

    /**
     * Gets the population of this place.
     *
     * @param bool $intval
     * @return string|int|null
     */
    public function getPopulation(bool $intval = false): string|int|null
    {
        return $intval ? intval($this->population) : $this->population;
    }

    /**
     * Sets the population of this place.
     *
     * @param string|int|null $population
     * @return $this
     */
    public function setPopulation(string|int|null $population): self
    {
        $this->population = strval($population);

        return $this;
    }

    /**
     * Gets the elevation of this place.
     *
     * @return int|null
     */
    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    /**
     * Sets the elevation of this place.
     *
     * @param int|null $elevation
     * @return $this
     */
    public function setElevation(?int $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Gets the dem of this place.
     *
     * @return int|null
     */
    public function getDem(): ?int
    {
        return $this->dem;
    }

    /**
     * Sets the dem of this place.
     *
     * @param int|null $dem
     * @return $this
     */
    public function setDem(?int $dem): self
    {
        $this->dem = $dem;

        return $this;
    }

    /**
     * Gets the timezone of this place.
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone of this place.
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Gets the modification date of this place.
     *
     * @return DateTime
     */
    public function getModificationDate(): DateTime
    {
        return $this->modificationDate;
    }

    /**
     * Sets the modification date of this place.
     *
     * @param DateTime $modificationDate
     * @return $this
     */
    public function setModificationDate(DateTime $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * Gets admin1 code of this place.
     *
     * @return string|null
     */
    public function getAdmin1Code(): ?string
    {
        return $this->admin1Code;
    }

    /**
     * Sets admin1 code of this place.
     *
     * @param string|null $admin1Code
     * @return $this
     */
    public function setAdmin1Code(?string $admin1Code): self
    {
        $this->admin1Code = $admin1Code;

        return $this;
    }

    /**
     * Gets admin2 code of this place.
     *
     * @return string|null
     */
    public function getAdmin2Code(): ?string
    {
        return $this->admin2Code;
    }

    /**
     * Sets admin2 code of this place.
     *
     * @param string|null $admin2Code
     * @return $this
     */
    public function setAdmin2Code(?string $admin2Code): self
    {
        $this->admin2Code = $admin2Code;

        return $this;
    }

    /**
     * Gets admin3 code of this place.
     *
     * @return string|null
     */
    public function getAdmin3Code(): ?string
    {
        return $this->admin3Code;
    }

    /**
     * Sets admin3 code of this place.
     *
     * @param string|null $admin3Code
     * @return $this
     */
    public function setAdmin3Code(?string $admin3Code): self
    {
        $this->admin3Code = $admin3Code;

        return $this;
    }

    /**
     * Gets admin4 code of this place.
     *
     * @return string|null
     */
    public function getAdmin4Code(): ?string
    {
        return $this->admin4Code;
    }

    /**
     * Sets admin4 code of this place.
     *
     * @param string|null $admin4Code
     * @return $this
     */
    public function setAdmin4Code(?string $admin4Code): self
    {
        $this->admin4Code = $admin4Code;

        return $this;
    }

    /**
     * Gets distance of this place (if given from select query to a given place). Not used for db.
     *
     * @return float
     */
    public function getDistanceDb(): float
    {
        return $this->distanceDb;
    }

    /**
     * Gets distance of this place (if given from select query to a given place). Not used for db.
     *
     * @param int $decimal
     * @param bool $withUnit
     * @return float|string
     */
    public function getDistanceDbInMeter(int $decimal = 1, bool $withUnit = true): float|string
    {
        $mDegree = 42000000 / 360;

        $distance = round($mDegree * $this->getDistanceDb(), $decimal);

        return $withUnit ? sprintf(sprintf('%%.%df m', $decimal), $distance) : $distance;
    }

    /**
     * Sets distance of this place (if given from select query to a given place). Not used for db.
     *
     * @param float $distanceDb
     * @return $this
     */
    public function setDistanceDb(float $distanceDb): self
    {
        $this->distanceDb = $distanceDb;

        return $this;
    }

    /**
     * Gets distance in m of this place (if given from select query to a given place). Not used for db.
     *
     * @param int|null $decimal
     * @param string|null $unit
     * @param int $divider
     * @return float|string
     */
    public function getDistanceMeter(?int $decimal = null, ?string $unit = null, int $divider = 1): float|string
    {
        $distanceMeter = $this->distanceMeter / $divider;

        if ($decimal !== null) {
            $distanceMeter = sprintf(sprintf('%%.%df', $decimal), $distanceMeter);
        }

        if ($unit) {
            $distanceMeter = str_replace('.', ',', sprintf('%s %s', $distanceMeter, $unit));
        }

        return $distanceMeter;
    }

    /**
     * Sets distance in m of this place (if given from select query to a given place). Not used for db.
     *
     * @param float $distanceMeter
     * @return Place
     */
    public function setDistanceMeter(float $distanceMeter): Place
    {
        $this->distanceMeter = $distanceMeter;
        return $this;
    }

    /**
     * Returns if distance is lower than given.
     *
     * @param Place $place
     * @param int $maxDistanceRural
     * @param int $maxDistanceCity
     * @return bool
     */
    public function withinTheDistance(Place $place, int $maxDistanceRural, int $maxDistanceCity): bool
    {
        return match (true) {
            $place->isCity() => $this->getDistanceMeter() <= $maxDistanceCity,
            default => $this->getDistanceMeter() <= $maxDistanceRural,
        };
    }

    /**
     * Gets city (P) of this place. Not used for db.
     *
     * @return PlaceP|null
     */
    public function getCityP(): ?PlaceP
    {
        return $this->cityP;
    }

    /**
     * Sets city (P) of this place. Not used for db.
     *
     * @param PlaceP|null $cityP
     * @return $this
     */
    public function setCityP(?PlaceP $cityP): self
    {
        $this->cityP = $cityP;

        return $this;
    }

    /**
     * Gets city (A) of this place. Not used for db.
     *
     * @return PlaceA|null
     */
    public function getCityA(): ?PlaceA
    {
        return $this->cityA;
    }

    /**
     * Sets city (A) of this place. Not used for db.
     *
     * @param PlaceA|null $cityA
     * @return $this
     */
    public function setCityA(?PlaceA $cityA): self
    {
        $this->cityA = $cityA;

        return $this;
    }

    /**
     * Gets district of this place. Not used for db.
     *
     * @return PlaceA|PlaceP|null
     */
    public function getDistrict(): PlaceA|PlaceP|null
    {
        return $this->district;
    }

    /**
     * Sets district of this place. Not used for db.
     *
     * @param PlaceA|PlaceP|null $district
     * @return $this
     */
    public function setDistrict(PlaceA|PlaceP|null $district): self
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Gets city of this place. Not used for db.
     *
     * @return PlaceA|PlaceP|null
     */
    public function getCity(): PlaceA|PlaceP|null
    {
        return $this->city;
    }

    /**
     * Sets city of this place. Not used for db.
     *
     * @param PlaceA|PlaceP|null $city
     * @return $this
     */
    public function setCity(PlaceA|PlaceP|null $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets state of this place. Not used for db.
     *
     * @return PlaceA|null
     */
    public function getState(): ?PlaceA
    {
        return $this->state;
    }

    /**
     * Sets state of this place. Not used for db.
     *
     * @param PlaceA|null $state
     * @return $this
     */
    public function setState(?PlaceA $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Gets parks of this place. Not used for db.
     *
     * @return PlaceL[]
     */
    public function getParks(): array
    {
        return $this->parks;
    }

    /**
     * Gets first park of this place. Not used for db.
     *
     * @param bool $checkDistance
     * @param Place|null $placeSource
     * @return PlaceL|null
     */
    public function getFirstPark(bool $checkDistance = false, ?Place $placeSource = null): ?PlaceL
    {
        if ($placeSource instanceof PlaceL) {
            return $placeSource;
        }

        if ($placeSource !== null) {
            return null;
        }

        if (count($this->parks) <= 0) {
            return null;
        }

        $firstPark = $this->parks[0];

        if (!$checkDistance || $firstPark->withinTheDistance($this, PlaceLoaderService::MAX_DISTANCE_PARK_METER_TITLE_RURAL, PlaceLoaderService::MAX_DISTANCE_PARK_METER_TITLE_CITY)) {
            return $firstPark;
        }

        return null;
    }

    /**
     * Sets parks of this place. Not used for db.
     *
     * @param PlaceL[] $parks
     * @return $this
     */
    public function setParks(array $parks): self
    {
        $this->parks = $parks;

        return $this;
    }

    /**
     * Adds park to this place. Not used for db.
     *
     * @param PlaceL $park
     * @return $this
     */
    public function addPark(PlaceL $park): self
    {
        if ($park->getDistanceMeter() > PlaceLoaderService::MAX_DISTANCE_PARK_METER_LIST_RURAL) {
            return $this;
        }

        $this->parks[] = $park;

        return $this;
    }

    /**
     * Gets places of this place. Not used for db.
     *
     * @return PlaceP[]
     */
    public function getPlaces(): array
    {
        return $this->places;
    }

    /**
     * Gets first place of this place. Not used for db.
     *
     * @param bool $checkDistance
     * @return PlaceP|null
     */
    public function getFirstPlace(bool $checkDistance = false): ?PlaceP
    {
        if (count($this->places) <= 0) {
            return null;
        }

        $firstPlace = $this->places[0];

        if (!$checkDistance || $firstPlace->withinTheDistance($this, PlaceLoaderService::MAX_DISTANCE_PLACE_METER_TITLE_RURAL, PlaceLoaderService::MAX_DISTANCE_PLACE_METER_TITLE_CITY)) {
            return $firstPlace;
        }

        return null;
    }

    /**
     * Sets places of this place. Not used for db.
     *
     * @param PlaceP[] $places
     * @return $this
     */
    public function setPlaces(array $places): self
    {
        $this->places = $places;

        return $this;
    }

    /**
     * Adds place to this place. Not used for db.
     *
     * @param PlaceP $place
     * @return $this
     */
    public function addPlace(PlaceP $place): self
    {
        if ($place->getDistanceMeter() > PlaceLoaderService::MAX_DISTANCE_PLACE_METER_LIST_RURAL) {
            return $this;
        }

        $this->places[] = $place;

        return $this;
    }

    /**
     * Gets spots of this place. Not used for db.
     *
     * @return PlaceS[]
     */
    public function getSpots(): array
    {
        return $this->spots;
    }

    /**
     * Gets first spot of this place. Not used for db.
     *
     * @param bool $checkDistance
     * @param Place|null $placeSource
     * @return PlaceS|null
     */
    public function getFirstSpot(bool $checkDistance = false, ?Place $placeSource = null): ?PlaceS
    {
        if ($placeSource instanceof PlaceS) {
            return $placeSource;
        }

        if ($placeSource !== null) {
            return null;
        }

        if (count($this->spots) <= 0) {
            return null;
        }

        $firstSpot = $this->spots[0];

        if (!$checkDistance || $firstSpot->withinTheDistance($this, PlaceLoaderService::MAX_DISTANCE_SPOT_METER_TITLE_RURAL, PlaceLoaderService::MAX_DISTANCE_SPOT_METER_TITLE_CITY)) {
            return $firstSpot;
        }

        return null;
    }

    /**
     * Sets spots of this place. Not used for db.
     *
     * @param PlaceS[] $spots
     * @return $this
     */
    public function setSpots(array $spots): self
    {
        $this->spots = $spots;
        return $this;
    }

    /**
     * Adds spot to this place. Not used for db.
     *
     * @param PlaceS $spot
     * @return $this
     */
    public function addSpot(PlaceS $spot): self
    {
        if ($spot->getDistanceMeter() > PlaceLoaderService::MAX_DISTANCE_SPOT_METER_LIST_RURAL) {
            return $this;
        }

        $this->spots[] = $spot;

        return $this;
    }

    /**
     * Gets mountains of this place. Not used for db.
     *
     * @return PlaceT[]
     */
    public function getMountains(): array
    {
        return $this->mountains;
    }

    /**
     * Gets first mountain of this place. Not used for db.
     *
     * @param bool $checkDistance
     * @param Place|null $placeSource
     * @return PlaceT|null
     */
    public function getFirstMountain(bool $checkDistance = false, ?Place $placeSource = null): ?PlaceT
    {
        if ($placeSource instanceof PlaceT) {
            return $placeSource;
        }

        if ($placeSource !== null) {
            return null;
        }

        if (count($this->mountains) <= 0) {
            return null;
        }

        $firstMountain = $this->mountains[0];

        if (!$checkDistance || $firstMountain->withinTheDistance($this, PlaceLoaderService::MAX_DISTANCE_MOUNTAIN_METER_TITLE_RURAL, PlaceLoaderService::MAX_DISTANCE_MOUNTAIN_METER_TITLE_CITY)) {
            return $firstMountain;
        }

        return null;
    }

    /**
     * Sets mountains of this place. Not used for db.
     *
     * @param PlaceT[] $mountains
     * @return $this
     */
    public function setMountains(array $mountains): self
    {
        $this->mountains = $mountains;
        return $this;
    }

    /**
     * Adds mountain to this place. Not used for db.
     *
     * @param PlaceT $mountain
     * @return $this
     */
    public function addMountain(PlaceT $mountain): self
    {
        if ($mountain->getDistanceMeter() > PlaceLoaderService::MAX_DISTANCE_MOUNTAIN_METER_LIST_RURAL) {
            return $this;
        }

        $this->mountains[] = $mountain;

        return $this;
    }

    /**
     * Gets forests of this place. Not used for db.
     *
     * @return PlaceV[]
     */
    public function getForests(): array
    {
        return $this->forests;
    }

    /**
     * Gets first forest of this place. Not used for db.
     *
     * @param bool $checkDistance
     * @param Place|null $placeSource
     * @return PlaceV|null
     */
    public function getFirstForest(bool $checkDistance = false, ?Place $placeSource = null): ?PlaceV
    {
        if ($placeSource instanceof PlaceV) {
            return $placeSource;
        }

        if ($placeSource !== null) {
            return null;
        }

        if (count($this->forests) <= 0) {
            return null;
        }

        $firstForest = $this->forests[0];

        if (!$checkDistance || $firstForest->withinTheDistance($this, PlaceLoaderService::MAX_DISTANCE_FOREST_METER_TITLE_RURAL, PlaceLoaderService::MAX_DISTANCE_FOREST_METER_TITLE_CITY)) {
            return $firstForest;
        }

        return null;
    }

    /**
     * Sets forests of this place. Not used for db.
     *
     * @param PlaceV[] $forests
     * @return $this
     */
    public function setForests(array $forests): self
    {
        $this->forests = $forests;

        return $this;
    }

    /**
     * Adds forest to this place. Not used for db.
     *
     * @param PlaceV $forest
     * @return $this
     */
    public function addForest(PlaceV $forest): self
    {
        if ($forest->getDistanceMeter() > PlaceLoaderService::MAX_DISTANCE_FOREST_METER_LIST_RURAL) {
            return $this;
        }

        $this->forests[] = $forest;

        return $this;
    }

    /**
     * Gets the translated country name of this place. Not used for db.
     *
     * @param bool $detailed
     * @return string|null
     */
    public function getCountry(bool $detailed = false): ?string
    {
        if ($this->country === null) {
            return null;
        }

        return $detailed ? sprintf('%s (COUNTRY)', $this->country) : $this->country;
    }

    /**
     * Sets the translated country name of this place. Not used for db.
     *
     * @param string $country
     * @return $this
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Gets the admin (city) population of this place. Not used for db.
     *
     * @param bool $intval
     * @return string|int|null
     */
    public function getPopulationAdmin(bool $intval = false): string|int|null
    {
        return $intval ? intval($this->populationAdmin) : $this->populationAdmin;
    }

    /**
     * Sets the admin (city) population of this place. Not used for db.
     *
     * @param string|int|null $populationAdmin
     * @return $this
     */
    public function setPopulationAdmin(string|int|null $populationAdmin): self
    {
        $this->populationAdmin = strval($populationAdmin);

        return $this;
    }

    /**
     * Returns if this place is a city. Not used for db.
     *
     * @return bool
     */
    public function isCity(): bool
    {
        return $this->isCity;
    }

    /**
     * Sets if this place is a city. Not used for db.
     *
     * @param bool $isCity
     * @return Place
     */
    public function setIsCity(bool $isCity): Place
    {
        $this->isCity = $isCity;
        return $this;
    }

    /**
     * Gets the template add name. Not used for db.
     *
     * @return string
     */
    public function getTemplateAddName(): string
    {
        return $this->templateAddName;
    }

    /**
     * Sets the template add name. Not used for db.
     *
     * @param string $templateAddName
     * @return $this
     */
    public function setTemplateAddName(string $templateAddName): self
    {
        $this->templateAddName = $templateAddName;

        return $this;
    }

    /**
     * Returns google link. Not used for db.
     *
     * @return string
     * @throws Exception
     */
    #[ArrayShape(['longitude' => "string", 'latitude' => "string"])]
    public function getGoogleLink(): string
    {
        $coordinateTranslated = new Point($this->coordinate->getX(), $this->coordinate->getY(), $this->coordinate->getSrid());

        return GPSConverter::decimalDegree2google(
            $coordinateTranslated->getLongitude(),
            $coordinateTranslated->getLatitude(),
            $coordinateTranslated->getLongitudeDirection(),
            $coordinateTranslated->getLatitudeDirection()
        );
    }

    /**
     * Case insensitive $this->strContains.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    protected function strContains(string $haystack, string $needle): bool
    {
        return str_contains(strtolower($haystack), strtolower($needle));
    }
}
