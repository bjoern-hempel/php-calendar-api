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

namespace App\Service\Entity;

use App\Entity\Place;
use App\Entity\PlaceA;
use App\Entity\PlaceH;
use App\Entity\PlaceL;
use App\Entity\PlaceP;
use App\Entity\PlaceR;
use App\Entity\PlaceS;
use App\Entity\PlaceT;
use App\Entity\PlaceU;
use App\Entity\PlaceV;
use App\Repository\PlaceARepository;
use App\Repository\PlaceHRepository;
use App\Repository\PlaceLRepository;
use App\Repository\PlacePRepository;
use App\Repository\PlaceRRepository;
use App\Repository\PlaceSRepository;
use App\Repository\PlaceTRepository;
use App\Repository\PlaceURepository;
use App\Repository\PlaceVRepository;
use App\Utils\StringConverter;
use App\Utils\Timer;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTimeImmutable;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PlaceLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-21)
 * @package App\Command
 */
class PlaceLoaderService
{
    protected bool $debug = false;
    protected bool $verbose = false;

    public const FEATURE_CLASS_A = 'A'; /* country, state, region,... */
    public const FEATURE_CLASS_H = 'H'; /* stream, lake, ... */
    public const FEATURE_CLASS_L = 'L'; /* parks, area, ... */
    public const FEATURE_CLASS_P = 'P'; /* city, village, ... */
    public const FEATURE_CLASS_R = 'R'; /* road, railroad, ... */
    public const FEATURE_CLASS_S = 'S'; /* spot, building, farm, ... */
    public const FEATURE_CLASS_T = 'T'; /* mountain, hill, rock,... */
    public const FEATURE_CLASS_U = 'U'; /* undersea */
    public const FEATURE_CLASS_V = 'V'; /* forest, heath, ... */

    public const FEATURE_CLASSES_ALL = [
        self::FEATURE_CLASS_A,
        self::FEATURE_CLASS_H,
        self::FEATURE_CLASS_L,
        self::FEATURE_CLASS_P,
        self::FEATURE_CLASS_R,
        self::FEATURE_CLASS_S,
        self::FEATURE_CLASS_T,
        self::FEATURE_CLASS_U,
        self::FEATURE_CLASS_V,
    ];

    /* @see http://www.geonames.org/export/codes.html */
    public const FEATURE_CODE_P_PPL = 'PPL'; /* populated place; a city, town, village, or other agglomeration of buildings where people live and work */
    public const FEATURE_CODE_P_PPLA = 'PPLA'; /* seat of a first-order administrative division; seat of a first-order administrative division (PPLC takes precedence over PPLA) */
    public const FEATURE_CODE_P_PPLA2 = 'PPLA2'; /* seat of a second-order administrative division */
    public const FEATURE_CODE_P_PPLA3 = 'PPLA3'; /* seat of a third-order administrative division */
    public const FEATURE_CODE_P_PPLA4 = 'PPLA4'; /* seat of a fourth-order administrative division */
    public const FEATURE_CODE_P_PPLA5 = 'PPLA5'; /* seat of a fifth-order administrative division */
    public const FEATURE_CODE_P_PPLC = 'PPLC'; /* PPLC; capital of a political entity */
    public const FEATURE_CODE_P_PPLCH = 'PPLCH'; /* historical capital of a political entity; a former capital of a political entity */
    public const FEATURE_CODE_P_PPLF = 'PPLF'; /* farm village; a populated place where the population is largely engaged in agricultural activities */
    public const FEATURE_CODE_P_PPLG = 'PPLG'; /* seat of government of a political entity */
    public const FEATURE_CODE_P_PPLH = 'PPLH'; /* historical populated place; a populated place that no longer exists */
    public const FEATURE_CODE_P_PPLL = 'PPLL'; /* populated locality; an area similar to a locality but with a small group of dwellings or other buildings */
    public const FEATURE_CODE_P_PPLQ = 'PPLQ'; /* abandoned populated place */
    public const FEATURE_CODE_P_PPLR = 'PPLR'; /* religious populated place; a populated place whose population is largely engaged in religious occupations */
    public const FEATURE_CODE_P_PPLS = 'PPLS'; /* populated places; cities, towns, villages, or other agglomerations of buildings where people live and work */
    public const FEATURE_CODE_P_PPLW = 'PPLW'; /* destroyed populated place; a village, town or city destroyed by a natural disaster, or by war */
    public const FEATURE_CODE_P_PPLX = 'PPLX'; /* section of populated place */
    public const FEATURE_CODE_P_STLMT = 'STLMT'; /* israeli settlement */

    public const FEATURE_CODE_A_ADM1 = 'ADM1'; /* first-order administrative division; a primary administrative division of a country, such as a state in the United States */
    public const FEATURE_CODE_A_ADM2 = 'ADM2';
    public const FEATURE_CODE_A_ADM3 = 'ADM3';
    public const FEATURE_CODE_A_ADM4 = 'ADM4';

    public const FEATURE_CODES_ALL = [
        self::FEATURE_CLASS_A => [
            self::FEATURE_CODE_A_ADM1,
            self::FEATURE_CODE_A_ADM2,
            self::FEATURE_CODE_A_ADM3,
            self::FEATURE_CODE_A_ADM4,
        ],
        self::FEATURE_CLASS_H => [],
        self::FEATURE_CLASS_L => [],
        self::FEATURE_CLASS_P => [
            self::FEATURE_CODE_P_PPL,
            self::FEATURE_CODE_P_PPLA,#
            self::FEATURE_CODE_P_PPLA2,#
            self::FEATURE_CODE_P_PPLA3,#
            self::FEATURE_CODE_P_PPLA4,#
            self::FEATURE_CODE_P_PPLA5,#
            self::FEATURE_CODE_P_PPLC,#
            self::FEATURE_CODE_P_PPLCH,
            self::FEATURE_CODE_P_PPLF,
            self::FEATURE_CODE_P_PPLG,
            self::FEATURE_CODE_P_PPLH,
            self::FEATURE_CODE_P_PPLL,
            self::FEATURE_CODE_P_PPLQ,
            self::FEATURE_CODE_P_PPLR,
            self::FEATURE_CODE_P_PPLS,
            self::FEATURE_CODE_P_PPLW,
            self::FEATURE_CODE_P_PPLX,
            self::FEATURE_CODE_P_STLMT,
        ],
        self::FEATURE_CLASS_R => [],
        self::FEATURE_CLASS_S => [],
        self::FEATURE_CLASS_T => [],
        self::FEATURE_CLASS_U => [],
        self::FEATURE_CLASS_V => [],
    ];

    public const FEATURE_CODES_P_ADMIN_PLACES = [
        self::FEATURE_CODE_P_PPLA,
        self::FEATURE_CODE_P_PPLA2,
        self::FEATURE_CODE_P_PPLA3,
        self::FEATURE_CODE_P_PPLA4,
        self::FEATURE_CODE_P_PPLA5,
        self::FEATURE_CODE_P_PPLC
    ];

    public const FEATURE_CODES_P_DISTRICT_PLACES = [
        self::FEATURE_CODE_P_PPL,
        self::FEATURE_CODE_P_PPLX
    ];

    /* 40.000km: 40.000.000m / 360° * 0.01 = 1111.1m → Parks must be less than 1111.1m away. Otherwise, it will not be displayed. */
    protected const MAX_DISTANCE_PARKS = .01;

    /* 40.000km: 40.000.000m / 360° * 0.01 = 1111.1m → Parks must be less than 1111.1m away. Otherwise, it will not be displayed. */
    protected const MAX_DISTANCE_FOREST = .01;

    /* 40.000km: 40.000.000m / 360° * 0.01 = 1111.1m → Parks must be less than 1111.1m away. Otherwise, it will not be displayed. */
    protected const MAX_DISTANCE_MOUNTAIN = .01;

    /* 40.000km: 40.000.000m / 360° * 0.001 = 111.1m → Point of interest must be less than 111.1m away. Otherwise, it will not be displayed. */
    protected const MAX_DISTANCE_SPOT = .001;

    protected const RAW_SQL_POSITION = <<<SQL
SELECT
  p.*,
  X(p.`coordinate`) AS "latitude",
  Y(p.`coordinate`) AS "longitude",
  (
    GLength(
      LineStringFromWKB(
        LineString(
          p.`coordinate`, 
          GeomFromText('POINT(%f %f)')
        )
      )
    )
  )
  AS distance
FROM %s p
WHERE p.`feature_class` = '%s'%s%s%s%s%s%s
ORDER BY distance ASC
LIMIT %d;
SQL;

    private EntityManagerInterface $em;

    protected TranslatorInterface $translator;

    protected ?PlaceARepository $placeARepository;

    protected ?PlaceHRepository $placeHRepository;

    protected ?PlaceLRepository $placeLRepository;

    protected ?PlacePRepository $placePRepository;

    protected ?PlaceRRepository $placeRRepository;

    protected ?PlaceSRepository $placeSRepository;

    protected ?PlaceTRepository $placeTRepository;

    protected ?PlaceURepository $placeURepository;

    protected ?PlaceVRepository $placeVRepository;

    /**
     * PlaceLoaderService constructor.
     *
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param PlaceARepository|null $placeARepository
     * @param PlaceHRepository|null $placeHRepository
     * @param PlaceLRepository|null $placeLRepository
     * @param PlacePRepository|null $placePRepository
     * @param PlaceRRepository|null $placeRRepository
     * @param PlaceSRepository|null $placeSRepository
     * @param PlaceTRepository|null $placeTRepository
     * @param PlaceURepository|null $placeURepository
     * @param PlaceVRepository|null $placeVRepository
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, ?PlaceARepository $placeARepository = null, ?PlaceHRepository $placeHRepository = null, ?PlaceLRepository $placeLRepository = null, ?PlacePRepository $placePRepository = null, ?PlaceRRepository $placeRRepository = null, ?PlaceSRepository $placeSRepository = null, ?PlaceTRepository $placeTRepository = null, ?PlaceURepository $placeURepository = null, ?PlaceVRepository $placeVRepository = null)
    {
        $this->em = $em;

        $this->translator = $translator;

        $this->placeARepository = $placeARepository;
        $this->placeHRepository = $placeHRepository;
        $this->placeLRepository = $placeLRepository;
        $this->placePRepository = $placePRepository;
        $this->placeRRepository = $placeRRepository;
        $this->placeSRepository = $placeSRepository;
        $this->placeTRepository = $placeTRepository;
        $this->placeURepository = $placeURepository;
        $this->placeVRepository = $placeVRepository;
    }

    /**
     * Set debug mode.
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
     * Set verbose mode.
     *
     * @param bool $verbose
     * @return $this
     */
    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * Returns the entity manager.
     *
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * Checks that all needles are available within given haystack.
     *
     * @param string[] $needles
     * @param string[] $haystack
     * @return bool
     */
    protected function inArray(?array $needles, array $haystack): bool
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }

        foreach ($needles as $needle) {
            if (!in_array($needle, $haystack)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return new place instance.
     *
     * @param string $featureClass
     * @return PlaceA|PlaceH|PlaceL|PlaceP|PlaceR|PlaceS|PlaceT|PlaceU|PlaceV
     * @throws Exception
     */
    public static function getPlace(string $featureClass): PlaceS|PlaceL|PlaceP|PlaceT|PlaceH|PlaceR|PlaceV|PlaceU|PlaceA
    {
        return match ($featureClass) {
            self::FEATURE_CLASS_A => new PlaceA(),
            self::FEATURE_CLASS_H => new PlaceH(),
            self::FEATURE_CLASS_L => new PlaceL(),
            self::FEATURE_CLASS_P => new PlaceP(),
            self::FEATURE_CLASS_R => new PlaceR(),
            self::FEATURE_CLASS_S => new PlaceS(),
            self::FEATURE_CLASS_T => new PlaceT(),
            self::FEATURE_CLASS_U => new PlaceU(),
            self::FEATURE_CLASS_V => new PlaceV(),
            default => throw new Exception(sprintf('Unsupported feature class "%s" (%s:%d).', $featureClass, __FILE__, __LINE__)),
        };
    }

    /**
     * Builds place from given row.
     *
     * @param array<string,mixed> $row
     * @param string $featureClass
     * @return PlaceA|PlaceH|PlaceL|PlaceP|PlaceR|PlaceS|PlaceT|PlaceU|PlaceV
     * @throws Exception
     */
    protected function buildPlaceFromRow(array $row, string $featureClass = self::FEATURE_CLASS_A): PlaceA|PlaceH|PlaceL|PlaceP|PlaceR|PlaceS|PlaceT|PlaceU|PlaceV
    {
        $place = self::getPlace($featureClass);

        $place->setIdTmp(intval($row['id']));
        $place->setGeonameId(intval($row['geoname_id']));
        $place->setName(strval($row['name']));
        $place->setAsciiName(strval($row['ascii_name']));
        $place->setAlternateNames(strval($row['alternate_names']));
        $place->setCoordinate(new Point(floatval($row['latitude']), floatval($row['longitude'])));
        $place->setFeatureClass(strval($row['feature_class']));
        $place->setFeatureCode(strval($row['feature_code']));
        $place->setCountryCode(strval($row['country_code']));
        $place->setCc2(strval($row['cc2']));
        $place->setPopulation(strval($row['population']));
        $place->setElevation(intval($row['elevation']));
        $place->setDem(!empty($row['dem']) ? intval($row['dem']) : null);
        $place->setTimezone(strval($row['timezone']));
        $place->setModificationDate(StringConverter::convertDateTime(strval($row['modification_date'])));
        $place->setCreatedAt(DateTimeImmutable::createFromMutable(StringConverter::convertDateTime(strval($row['created_at']))));
        $place->setUpdatedAt(DateTimeImmutable::createFromMutable(StringConverter::convertDateTime(strval($row['updated_at']))));
        $place->setDistance(floatval($row['distance']));
        $place->setAdmin1Code(!empty($row['admin1_code']) ? strval($row['admin1_code']) : null);
        $place->setAdmin2Code(!empty($row['admin2_code']) ? strval($row['admin2_code']) : null);
        $place->setAdmin3Code(!empty($row['admin3_code']) ? strval($row['admin3_code']) : null);
        $place->setAdmin4Code(!empty($row['admin4_code']) ? strval($row['admin4_code']) : null);

        return $place;
    }

    /**
     * Builds raw sql query for position requests.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param string $featureClass
     * @param string|string[]|null $featureCodes
     * @param string|null $countryCode
     * @param string|null $adminCode1
     * @param string|null $adminCode2
     * @param string|null $adminCode3
     * @param string|null $adminCode4
     * @return string
     * @throws Exception
     */
    protected function getRawSqlPosition(
        float $latitude,
        float $longitude,
        int $limit = 1,
        string $featureClass = self::FEATURE_CLASS_P,
        string|array|null $featureCodes = null,
        ?string $countryCode = null,
        ?string $adminCode1 = null,
        ?string $adminCode2 = null,
        ?string $adminCode3 = null,
        ?string $adminCode4 = null
    ): string {
        if (is_string($featureCodes)) {
            $featureCodes = [$featureCodes];
        }

        if (!array_key_exists($featureClass, self::FEATURE_CODES_ALL)) {
            throw new Exception(sprintf('Unable to find feature class set with feature code "%s" (%s:%d).', $featureClass, __FILE__, __LINE__));
        }

        $featureCodesAll = self::FEATURE_CODES_ALL[$featureClass];

        /* @see http://www.geonames.org/export/codes.html */
        if ($this->inArray($featureCodes, $featureCodesAll)) {
            $sqlWhereFeatureCode = sprintf(' AND p.`feature_code` IN (\'%s\')', implode('\', \'', $featureCodes));
        } else {
            $sqlWhereFeatureCode = '';
        }

        if ($countryCode !== null) {
            $sqlWhereCountryCode = sprintf(' AND p.`country_code` = \'%s\'', $countryCode);
        } else {
            $sqlWhereCountryCode = '';
        }

        if ($adminCode1 !== null) {
            $sqlWhereAdmin1Code = sprintf(' AND p.`admin1_code` = \'%s\'', $adminCode1);
        } else {
            $sqlWhereAdmin1Code = '';
        }

        if ($adminCode2 !== null) {
            $sqlWhereAdmin2Code = sprintf(' AND p.`admin2_code` = \'%s\'', $adminCode2);
        } else {
            $sqlWhereAdmin2Code = '';
        }

        if ($adminCode3 !== null) {
            $sqlWhereAdmin3Code = sprintf(' AND p.`admin3_code` = \'%s\'', $adminCode3);
        } else {
            $sqlWhereAdmin3Code = '';
        }

        if ($adminCode4 !== null) {
            $sqlWhereAdmin4Code = sprintf(' AND p.`admin4_code` = \'%s\'', $adminCode4);
        } else {
            $sqlWhereAdmin4Code = '';
        }

        $tableName = sprintf('place_%s', strtolower($featureClass));

        $sqlRaw = sprintf(
            self::RAW_SQL_POSITION,
            $latitude,
            $longitude,
            $tableName,
            $featureClass,
            $sqlWhereFeatureCode,
            $sqlWhereCountryCode,
            $sqlWhereAdmin1Code,
            $sqlWhereAdmin2Code,
            $sqlWhereAdmin3Code,
            $sqlWhereAdmin4Code,
            $limit
        );

        return $sqlRaw;
    }

    /**
     * Returns raw query to get a PlaceA object ordered by location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param PlaceP $placeP
     * @return string
     * @throws Exception
     */
    protected function getRawQueryPlaceAFromPlaceP(float $latitude, float $longitude, PlaceP $placeP): string
    {
        return match ($placeP->getCountryCode()) {
            'AT', 'CH', 'ES', 'PL' => $this->getRawSqlPosition($latitude, $longitude, 1, self::FEATURE_CLASS_A, self::FEATURE_CODE_A_ADM3, $placeP->getCountry(), null, null, $placeP->getAdmin3Code(), $placeP->getAdmin4Code()),
            default => $this->getRawSqlPosition($latitude, $longitude, 1, self::FEATURE_CLASS_A, self::FEATURE_CODE_A_ADM4, $placeP->getCountry(), null, null, null, $placeP->getAdmin4Code()),
        };
    }

    /**
     * Find city from places (where population > 0).
     *
     * @param PlaceP[] $places
     * @return PlaceP|null
     */
    public function findCityByPlacesP(array $places): ?PlaceP
    {
        foreach ($places as $place) {
            if ($place->getPopulation() > 0) {
                return $place;
            }
        }

        return null;
    }

    /**
     * Returns country name by given place.
     *
     * @param PlaceP $placeP
     * @return string
     */
    public function getCountryByPlaceP(PlaceP $placeP): string
    {
        $countryCode = strtolower($placeP->getCountryCode());

        return $this->translator->trans(sprintf('country.alpha2.%s', $countryCode), [], 'countries', $countryCode);
    }

    /**
     * Returns another PlaceP entry, if given entry has no population.
     *
     * @param PlaceP $placeP
     * @param PlaceP[] $placesP
     * @return PlaceP|null
     */
    public function getCityPWithPopulationFromPlacesP(PlaceP $placeP, array $placesP): ?PlaceP
    {
        if ($placeP->getPopulation(true) > 0) {
            return null;
        }

        $cityP = $this->findCityByPlacesP($placesP);

        if ($cityP !== null) {
            $placeP->setCityP($cityP);
            if ($cityP->getPopulation() !== null) {
                $placeP->setPopulation($cityP->getPopulation());
            }
        }

        return $cityP;
    }

    /**
     * Gets a PlaceA entry (city) from Place P
     *
     * @param PlaceP $placeP
     * @return PlaceA|null
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getCityByPlacePFromAdmin(PlaceP $placeP): ?PlaceA
    {
        if ($this->placeARepository === null) {
            return null;
        }

        $cityATimer = Timer::start();
        $cityA = $this->placeARepository->findCityByPlaceP($placeP);
        $cityATime = Timer::stop($cityATimer);
        $this->printRawQuery('cityA', $this->placeARepository->getLastSQL(), $cityATime);

        return $cityA;
    }

    /**
     * Gets the state from cityA.
     *
     * @param PlaceP|null $placeP
     * @return PlaceA|null
     * @throws NonUniqueResultException
     */
    public function getStateFromPlaceP(PlaceP|null $placeP): ?PlaceA
    {
        if ($placeP === null) {
            return null;
        }

        if ($this->placeARepository === null) {
            return null;
        }

        $stateTimer = Timer::start();
        $state = $this->placeARepository->findStateByPlaceP($placeP);
        $stateTime = Timer::stop($stateTimer);
        $this->printRawQuery('state', $this->placeARepository->getLastSQL(), $stateTime);

        return $state;
    }

    /**
     * Finds next admin place.
     *
     * @param PlaceP[] $placesP
     * @param PlaceP $district
     * @return PlaceP|null
     */
    protected function findNextAdminCity(array $placesP, PlaceP $district): ?PlaceP
    {
        foreach ($placesP as $placeP) {
            switch (true) {
                case in_array($placeP->getFeatureCode(), self::FEATURE_CODES_P_ADMIN_PLACES) && $district->getAdmin4Code() == $placeP->getAdmin4Code():
                    return $placeP;
            }
        }

        return null;
    }

    /**
     * Finds next place with more than 0 population.
     *
     * @param PlaceP[] $placesP
     * @param PlaceP $district
     * @return PlaceP|null
     */
    protected function findNextCityPopulation(array $placesP, PlaceP $district): ?PlaceP
    {
        if ($district->getPopulation(true) > 0) {
            return null;
        }

        foreach ($placesP as $placeP) {
            switch (true) {
                case $placeP->getPopulation(true) > 0 && $district->getAdmin4Code() == $placeP->getAdmin4Code():
                    $district->setPopulation($placeP->getPopulation());
                    return $placeP;
            }
        }

        return null;
    }

    /**
     * Finds next admin place.
     *
     * @param PlaceP[] $placesP
     * @param PlaceP $city
     * @return PlaceP|null
     */
    protected function findNextDistrict(array $placesP, PlaceP $city): ?PlaceP
    {
        foreach ($placesP as $placeP) {
            switch (true) {
                case in_array($placeP->getFeatureCode(), self::FEATURE_CODES_P_DISTRICT_PLACES) && $city->getAdmin4Code() == $placeP->getAdmin4Code():
                    return $placeP;
            }
        }

        return null;
    }

    /**
     * Finds the nearest place by coordinate.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string|string[]|null $featureCodes
     * @return ?PlaceP
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function findPlacePByPosition(float $latitude, float $longitude, string|array|null $featureCodes = null): ?PlaceP
    {
        if ($this->placeARepository === null) {
            return null;
        }

        $connection = $this->getEntityManager()->getConnection();
        $featureClass = self::FEATURE_CLASS_P;

        /* Find feature class P */
        $cityPTimer = Timer::start();
        $sqlRaw = $this->getRawSqlPosition($latitude, $longitude, 50, $featureClass, $featureCodes);
        $statement = $connection->prepare($sqlRaw);
        $result = $statement->executeQuery();
        $cityPTime = Timer::stop($cityPTimer);
        $this->printRawQuery('places', $sqlRaw, $cityPTime);

        /* Reads all results. */
        /** @var PlaceP[] $placesP */
        $placesP = [];
        while (($row = $result->fetchAssociative()) !== false) {

            /* Build and add place. */
            $placeP = $this->buildPlaceFromRow($row, $featureClass);

            if (!$placeP instanceof PlaceP) {
                throw new Exception(sprintf('Unexpected place instance "%s" (%s:%d).', get_class($placeP), __FILE__, __LINE__));
            }

            $placesP[] = $placeP;
        }

        /* No result was found. */
        if (count($placesP) === 0) {
            return null;
        }

        /* Verbose information */
        $this->printPlaces($placesP);

        $place = $placesP[0];
        $city = null;
        $district = null;
        switch (true) {
            case in_array($place->getFeatureCode(), self::FEATURE_CODES_P_DISTRICT_PLACES):
                $district = array_shift($placesP);
                $city1 = $this->getCityByPlacePFromAdmin($district);
                $city2 = $this->findNextAdminCity($placesP, $district);
                $city3 = $this->findNextCityPopulation($placesP, $district);

                switch (true) {
                    case $city1 === null:
                        $city = $city2 !== null && $city2->getPopulation(true) > 0 ? $city2 : $city3;
                        break;

                    default:
//                        /* Maspalomas vs Meloneras */
//                        if ($place->getCountryCode() === 'ES') {
//                            $city = $city1->getPopulation(true) > $city3->getPopulation(true) ? $city3 : $city1;
//                        } else {
//                            $city = $city1;
//                        }
                        $city = $city1;
                        break;
                }

                break;

            case in_array($place->getFeatureCode(), self::FEATURE_CODES_P_ADMIN_PLACES):
                $city = array_shift($placesP);
                $district = $this->findNextDistrict($placesP, $city);
                break;
        }

        $state = $this->getStateFromPlaceP($place);
        $country = $this->getCountryByPlaceP($place);

        $place->setDistrict($district);
        $place->setCity($city);
        $place->setState($state);
        $place->setCountry($country);

        /* Parks, Areas → L */
        $placesPark = $this->findByPosition($latitude, $longitude, 1, self::FEATURE_CLASS_L);
        foreach ($placesPark as $placePark) {
            if (!$placePark instanceof PlaceL) {
                throw new Exception(sprintf('Unexpected place instance "%s" (%s:%d).', get_class($placePark), __FILE__, __LINE__));
            }

            if ($placePark->getDistance() <= self::MAX_DISTANCE_PARKS) {
                $place->addPark($placePark);
            }
        }

        /* Forest → V */
        $placesForest = $this->findByPosition($latitude, $longitude, 1, self::FEATURE_CLASS_V);
        foreach ($placesForest as $placeForest) {
            if (!$placeForest instanceof PlaceV) {
                throw new Exception(sprintf('Unexpected place instance "%s" (%s:%d).', get_class($placeForest), __FILE__, __LINE__));
            }

            if ($placeForest->getDistance() <= self::MAX_DISTANCE_FOREST) {
                $place->addForest($placeForest);
            }
        }

        /* Mountain → T */
        $placesMountain = $this->findByPosition($latitude, $longitude, 1, self::FEATURE_CLASS_T);
        foreach ($placesMountain as $placeMountain) {
            if (!$placeMountain instanceof PlaceT) {
                throw new Exception(sprintf('Unexpected place instance "%s" (%s:%d).', get_class($placeMountain), __FILE__, __LINE__));
            }

            if ($placeMountain->getDistance() <= self::MAX_DISTANCE_MOUNTAIN) {
                $place->addMountain($placeMountain);
            }
        }

        /* Add point of interest → S */
        $placesSpot = $this->findByPosition($latitude, $longitude, 1, self::FEATURE_CLASS_S);
        foreach ($placesSpot as $placeSpot) {
            if (!$placeSpot instanceof PlaceS) {
                throw new Exception(sprintf('Unexpected place instance "%s" (%s:%d).', get_class($placeSpot), __FILE__, __LINE__));
            }

            if ($placeSpot->getDistance() <= self::MAX_DISTANCE_SPOT) {
                $place->addSpot($placeSpot);
            }
        }

        /* Verbose information */
        $this->printPlaceInformation($place);

        return $place;
    }

    /**
     * Finds the nearest place by coordinate.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param string $featureClass
     * @param string|string[]|null $featureCodes
     * @param string|null $countryCode
     * @param string|null $adminCode3
     * @param string|null $adminCode4
     * @return PlaceA[]|PlaceH[]|PlaceL[]|PlaceP[]|PlaceR[]|PlaceS[]|PlaceT[]|PlaceU[]|PlaceV[]
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function findByPosition(float $latitude, float $longitude, int $limit = 1, string $featureClass = self::FEATURE_CLASS_P, string|array|null $featureCodes = null, ?string $countryCode = null, ?string $adminCode3 = null, ?string $adminCode4 = null): array
    {
        $places = [];

        $connection = $this->getEntityManager()->getConnection();

        $positionTimer = Timer::start();
        $sqlRaw = $this->getRawSqlPosition($latitude, $longitude, $limit, $featureClass, $featureCodes, $countryCode, null, null, $adminCode3, $adminCode4);
        $statement = $connection->prepare($sqlRaw);
        $result = $statement->executeQuery();
        $positionTime = Timer::stop($positionTimer);
        $this->printRawQuery(sprintf('Place%s', $featureClass), $sqlRaw, $positionTime);

        while (($row = $result->fetchAssociative()) !== false) {

            /* Build place. */
            $place = $this->buildPlaceFromRow($row, $featureClass);

            $place->setName(ucfirst($place->getName()));

            $places[] = $place;
        }

        return $places;
    }

    /**
     * Use sprintf in a utf8 way.
     *
     * @param string $format
     * @param mixed ...$args
     * @return string
     * @throws Exception
     */
    protected function mbSprintf(string $format, ...$args): string
    {
        $params = $args;

        $callback = function ($length) use (&$params) {
            $value = array_shift($params);

            $value = match (true) {
                is_null($value) => '',
                default => strval($value),
            };

            return strlen($value) - mb_strlen($value) + $length[0];
        };

        /** @phpstan-ignore-next-line → PHPStan does not detect $callback as valid */
        $format = preg_replace_callback('/(?<=%|%-)\d+(?=s)/', $callback, $format);

        if ($format === null) {
            throw new Exception(sprintf('Unable to execute preg_replace_callback (%s:%d).', __FILE__, __LINE__));
        }

        foreach ($args as &$arg) {
            $arg = strval($arg);
        }

        /** @phpstan-ignore-next-line → All arguments are already converted into string. */
        return sprintf($format, ...$args);
    }

    /**
     * Prints raw query.
     *
     * @param string $name
     * @param string $sqlRaw
     * @param float $time
     * @return void
     */
    protected function printRawQuery(string $name, string $sqlRaw, float $time): void
    {
        if (!$this->debug) {
            return;
        }

        $title = sprintf('SQL "%s" - %.4fs', $name, $time);

        print $title."\n";
        print str_repeat('-', strlen($title))."\n";
        print $sqlRaw."\n";
        print str_repeat('-', strlen($title))."\n";
        print "\n";
    }

    /**
     * Prints next places.
     *
     * @param PlaceP[] $placesP
     * @return void
     * @throws Exception
     */
    protected function printPlaces(array $placesP): void
    {
        if (!$this->verbose) {
            return;
        }

        $title = 'Next 50 places';
        $format = '%-63s %-6s %6s %6s %6s %10s %10s %12s';

        print "\n";
        print $title."\n";
        print str_repeat('-', strlen($title))."\n";

        $caption = $this->mbSprintf($format, 'Name', 'FC', 'ADM1', 'ADM2', 'ADM3', 'ADM4', 'Population', 'Distance');

        print $caption."\n";
        print str_repeat('-', strlen($caption))."\n";
        foreach ($placesP as $placeP) {
            print $this->mbSprintf(
                $format,
                $placeP->getName(),
                $placeP->getFeatureCode(),
                $placeP->getAdmin1Code(),
                $placeP->getAdmin2Code(),
                $placeP->getAdmin3Code(),
                $placeP->getAdmin4Code(),
                $placeP->getPopulation(),
                $placeP->getDistanceInMeter()
            )."\n";
        }

        print "\n";
    }

    /**
     * Prints district, city, state and country.
     *
     * @param PlaceP $place
     * @return void
     * @throws Exception
     */
    protected function printPlaceInformation(PlaceP $place): void
    {
        if (!$this->verbose) {
            return;
        }

        $title = 'Place information';
        $format = '%-10s %-63s %-6s';
        $district = $place->getDistrict();
        $city = $place->getCity();
        $state = $place->getState();
        $country = $place->getCountry();

        $caption = $this->mbSprintf($format, '', 'Name', 'FC');

        print "\n";
        print $title."\n";
        print str_repeat('-', strlen($title))."\n";
        print $caption."\n";
        print str_repeat('-', strlen($caption))."\n";

        if ($district !== null) {
            print $this->mbSprintf($format, 'District', $district->getName(), $district->getFeatureCode())."\n";
        } else {
            print $this->mbSprintf($format, 'District', 'Kein Stadtteil gefunden.', '')."\n";
        }

        if ($city !== null) {
            print $this->mbSprintf($format, 'City', $city->getName(), $city->getFeatureCode())."\n";
        } else {
            print $this->mbSprintf($format, 'City', 'Keine Stadt gefunden.', '')."\n";
        }

        if ($state !== null) {
            print $this->mbSprintf($format, 'State', $state->getName(), $state->getFeatureCode())."\n";
        } else {
            print $this->mbSprintf($format, 'State', 'Keine Bundesland/Gemeinde gefunden.', '')."\n";
        }

        if ($country !== null) {
            print $this->mbSprintf($format, 'Country', $country, '')."\n";
        } else {
            print $this->mbSprintf($format, 'Country', 'Kein Land angegeben.', '')."\n";
        }

        print "\n";
    }
}
