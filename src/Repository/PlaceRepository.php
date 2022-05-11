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

namespace App\Repository;

use App\Entity\Place;
use App\Utils\StringConverter;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImageData
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-08)
 * @package App\Command
 * @extends ServiceEntityRepository<Place>
 *
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
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

    public const FEATURE_CODES_ALL = [
        self::FEATURE_CLASS_A => [
            self::FEATURE_CODE_A_ADM1,
        ],
        self::FEATURE_CLASS_H => [],
        self::FEATURE_CLASS_L => [],
        self::FEATURE_CLASS_P => [
            self::FEATURE_CODE_P_PPL,
            self::FEATURE_CODE_P_PPLA,
            self::FEATURE_CODE_P_PPLA2,
            self::FEATURE_CODE_P_PPLA3,
            self::FEATURE_CODE_P_PPLA4,
            self::FEATURE_CODE_P_PPLA5,
            self::FEATURE_CODE_P_PPLC,
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

    /* 40.000km: 40.000.000m / 360° * 0.01 = 1111.1m → Parks must be less than 1111.1m away. Otherwise, it will not be displayed. */
    protected const MAX_DISTANCE_PARKS = .01;

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
FROM place p
WHERE p.`feature_class` IN ('%s')%s%s%s%s
ORDER BY distance ASC
LIMIT %d;
SQL;

    protected TranslatorInterface $translator;

    /**
     * PlaceRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param TranslatorInterface $translator
     */
    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        $this->translator = $translator;

        parent::__construct($registry, Place::class);
    }

    /**
     * Returns Admin Place.
     *
     * @param string $countryCode
     * @param string $admin3Code
     * @return Place|null
     */
    protected function getAdminPlace(string $countryCode, string $admin3Code): ?Place
    {
        $adminPlaces = $this->findByCountryAndAdmin3Code($countryCode, $admin3Code, self::FEATURE_CLASS_P, [self::FEATURE_CODE_P_PPLX]);

        foreach (self::FEATURE_CODES_P_ADMIN_PLACES as $featureCode) {
            foreach ($adminPlaces as $adminPlace) {
                if ($adminPlace->getFeatureCode() === $featureCode) {
                    return $adminPlace;
                }
            }
        }

        return null;
    }

    /**
     * Tries to find out another place with more than 0 inhabitants.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $countryCode
     * @param string $adminCode
     * @param string $featureClass
     * @param int $type
     * @return Place|null
     * @throws Exception
     */
    protected function getOtherPlace(float $latitude, float $longitude, string $countryCode, string $adminCode, string $featureClass, int $type = 4): ?Place
    {
        $otherPlaces = match ($type) {
            3 => $this->findByPosition($latitude, $longitude, 10, $featureClass, null, $countryCode, $adminCode),
            4 => $this->findByPosition($latitude, $longitude, 10, $featureClass, null, $countryCode, null, $adminCode),
            default => throw new Exception(sprintf('Unsupported type "%d" (%s:%d).', $type, __FILE__, __LINE__)),
        };

        foreach ($otherPlaces as $otherPlace) {
            if (intval($otherPlace->getPopulation()) > 0) {
                return $otherPlace;
            }
        }

        return null;
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
     * Builds raw sql query for position requests.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param string|string[] $featureClasses
     * @param string|string[]|null $featureCodes
     * @param string|null $countryCode
     * @param string|null $adminCode3
     * @param string|null $adminCode4
     * @return string
     * @throws Exception
     */
    protected function getRawSqlPosition(
        float $latitude,
        float $longitude,
        int $limit = 1,
        string|array $featureClasses = self::FEATURE_CLASS_P,
        string|array|null $featureCodes = null,
        ?string $countryCode = null,
        ?string $adminCode3 = null,
        ?string $adminCode4 = null
    ): string {
        if (is_string($featureCodes)) {
            $featureCodes = [$featureCodes];
        }

        if (is_string($featureClasses)) {
            $featureClasses = [$featureClasses];
        }

        foreach ($featureClasses as $featureClass) {
            if (!array_key_exists($featureClass, self::FEATURE_CODES_ALL)) {
                throw new Exception(sprintf('Unable to find feature class set with feature code "%s" (%s:%d).', $featureClass, __FILE__, __LINE__));
            }
        }

        $featureCodesAll = [];
        foreach ($featureClasses as $featureClass) {
            $featureCodesAll = array_merge($featureCodesAll, self::FEATURE_CODES_ALL[$featureClass]);
        }

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

        return sprintf(
            self::RAW_SQL_POSITION,
            $latitude,
            $longitude,
            implode('\', \'', $featureClasses),
            $sqlWhereFeatureCode,
            $sqlWhereCountryCode,
            $sqlWhereAdmin3Code,
            $sqlWhereAdmin4Code,
            $limit
        );
    }

    /**
     * Builds place from given row.
     *
     * @param array<string,mixed> $row
     * @return Place
     */
    protected function buildPlaceFromRow(array $row): Place
    {
        $place = new Place();

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
     * Finds the nearest state by coordinates and country.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $countryCode
     * @return Place|null
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function findOneStateByPosition(float $latitude, float $longitude, string $countryCode): ?Place
    {
        $connection = $this->getEntityManager()->getConnection();

        $sqlRaw = $this->getRawSqlPosition($latitude, $longitude, 1, self::FEATURE_CLASS_A, self::FEATURE_CODE_A_ADM1, $countryCode);

        $statement = $connection->prepare($sqlRaw);
        $result = $statement->executeQuery();

        if (($row = $result->fetchAssociative()) !== false) {
            return $this->buildPlaceFromRow($row);
        }

        return null;
    }

    /**
     * Finds the nearest place by coordinate.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param string|string[] $featureClasses
     * @param string|string[]|null $featureCodes
     * @return Place[]
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function findPlaceByPosition(float $latitude, float $longitude, int $limit = 1, string|array $featureClasses = self::FEATURE_CLASS_P, string|array|null $featureCodes = null): array
    {
        $places = [];

        $connection = $this->getEntityManager()->getConnection();

        $sqlRaw = $this->getRawSqlPosition($latitude, $longitude, $limit, $featureClasses, $featureCodes);

        $statement = $connection->prepare($sqlRaw);
        $result = $statement->executeQuery();

        while (($row = $result->fetchAssociative()) !== false) {

            /* Build place. */
            $place = $this->buildPlaceFromRow($row);

            /* Try to find a place with more than 0 inhabitants. */
            if (intval($place->getPopulation()) <= 0) {
                $otherPlace = match (true) {
                    $place->getAdmin4Code() !== null => $this->getOtherPlace($latitude, $longitude, $place->getCountryCode(), $place->getAdmin4Code(), $place->getFeatureClass(), 4),
                    $place->getAdmin3Code() !== null => $this->getOtherPlace($latitude, $longitude, $place->getCountryCode(), $place->getAdmin3Code(), $place->getFeatureClass(), 3),
                    default => null,
                };

                if ($otherPlace instanceof Place) {
                    $place->setName(sprintf('%s, %s', $place->getName(), ucfirst($otherPlace->getName())));
                }
            }

            /* This is usually a district */
            if ($place->getFeatureCode() === self::FEATURE_CODE_P_PPLX && $place->getAdmin3Code() !== null) {
                $adminPlace = $this->getAdminPlace($place->getCountryCode(), $place->getAdmin3Code());

                if ($adminPlace instanceof Place) {
                    $place->setName(sprintf('%s, %s', $place->getName(), ucfirst($adminPlace->getName())));
                }
            }

            $state = $this->findOneStateByPosition($latitude, $longitude, $place->getCountryCode());

            if ($state instanceof Place) {
                $place->setName(sprintf('%s, %s', $place->getName(), ucfirst($state->getName())));
            }

            $place->setName(sprintf('%s, %s', $place->getName(), $this->translator->trans(sprintf('country.alpha2.%s', strtolower($place->getCountryCode())), [], 'countries')));

            $place->setName(ucfirst($place->getName()));

            /* Parks, Areas, Forest → L, V */
            $placesPark = $this->findByPosition($latitude, $longitude, 1, [self::FEATURE_CLASS_L, self::FEATURE_CLASS_V]);

            if (count($placesPark) >= 1) {
                $placePark = $placesPark[0];

                if ($placePark->getDistance() <= self::MAX_DISTANCE_PARKS) {
                    $name = $place->getName();

                    if (!str_contains($name, $placePark->getName())) {
                        $place->setName(sprintf('%s, %s', $placePark->getName(), $name));
                    }
                }
            }

            /* Mountain → T */
            $placesMountain = $this->findByPosition($latitude, $longitude, 1, [self::FEATURE_CLASS_T]);

            if (count($placesMountain) >= 1) {
                $placeMountain = $placesMountain[0];

                if ($placeMountain->getDistance() <= self::MAX_DISTANCE_MOUNTAIN) {
                    $name = $place->getName();

                    if (!str_contains($name, $placeMountain->getName())) {
                        $place->setName(sprintf('%s, %s', $placeMountain->getName(), $name));
                    }
                }
            }

            /* Add point of interest → S */
            $spotPlaces = $this->findByPosition($latitude, $longitude, 1, [self::FEATURE_CLASS_S]);

            if (count($spotPlaces) >= 1) {
                $spotPlace = $spotPlaces[0];

                if ($spotPlace->getDistance() <= self::MAX_DISTANCE_SPOT) {
                    $name = $place->getName();

                    if (!str_contains($name, $spotPlace->getName())) {
                        $place->setName(sprintf('%s, %s', $spotPlace->getName(), $name));
                    }
                }
            }

            $places[] = $place;
        }

        return $places;
    }

    /**
     * Finds the nearest place by coordinate.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $limit
     * @param string|string[] $featureClasses
     * @param string|string[]|null $featureCodes
     * @param string|null $countryCode
     * @param string|null $adminCode3
     * @param string|null $adminCode4
     * @return Place[]
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function findByPosition(float $latitude, float $longitude, int $limit = 1, string|array $featureClasses = self::FEATURE_CLASS_P, string|array|null $featureCodes = null, ?string $countryCode = null, ?string $adminCode3 = null, ?string $adminCode4 = null): array
    {
        $places = [];

        $connection = $this->getEntityManager()->getConnection();

        $sqlRaw = $this->getRawSqlPosition($latitude, $longitude, $limit, $featureClasses, $featureCodes, $countryCode, $adminCode3, $adminCode4);

        $statement = $connection->prepare($sqlRaw);
        $result = $statement->executeQuery();

        while (($row = $result->fetchAssociative()) !== false) {

            /* Build place. */
            $place = $this->buildPlaceFromRow($row);

            $place->setName(ucfirst($place->getName()));

            $places[] = $place;
        }

        return $places;
    }

    /**
     * Find by country code and admin3 code.
     *
     * @param string $countryCode
     * @param string $admin3Code
     * @param string|null $featureClass
     * @param string[]|null $ignoreFeatureCodes
     * @return Place[]
     */
    public function findByCountryAndAdmin3Code(string $countryCode, string $admin3Code, ?string $featureClass = null, ?array $ignoreFeatureCodes = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($ignoreFeatureCodes !== null) {
            $queryBuilder->where($queryBuilder->expr()->notIn('p.featureCode', $ignoreFeatureCodes));
        }

        if ($featureClass !== null) {
            $queryBuilder->andWhere('p.featureClass = :fc')
                ->setParameter('fc', $featureClass);
        }

        $queryBuilder->andWhere('p.countryCode = :cc')
            ->setParameter('cc', $countryCode);

        $queryBuilder->andWhere('p.admin3Code = :ac')
            ->setParameter('ac', $admin3Code);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find by country code and admin4 code.
     *
     * @param string $countryCode
     * @param string $admin4Code
     * @param string|null $featureClass
     * @param string[]|null $ignoreFeatureCodes
     * @return Place[]
     */
    public function findByCountryAndAdmin4Code(string $countryCode, string $admin4Code, ?string $featureClass = null, ?array $ignoreFeatureCodes = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($ignoreFeatureCodes !== null) {
            $queryBuilder->where($queryBuilder->expr()->notIn('p.featureCode', $ignoreFeatureCodes));
        }

        if ($featureClass !== null) {
            $queryBuilder->andWhere('p.featureClass = :fc')
                ->setParameter('fc', $featureClass);
        }

        $queryBuilder->andWhere('p.countryCode = :cc')
            ->setParameter('cc', $countryCode);

        $queryBuilder->andWhere('p.admin4Code = :ac')
            ->setParameter('ac', $admin4Code);

        return $queryBuilder->getQuery()->getResult();
    }
}
