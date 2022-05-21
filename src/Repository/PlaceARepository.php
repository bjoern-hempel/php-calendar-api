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

use App\Entity\PlaceA;
use App\Entity\PlaceP;
use App\Repository\Base\PlaceRepositoryInterface;
use App\Service\Entity\PlaceLoaderService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class PlaceARepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-20)
 * @package App\Command
 * @extends ServiceEntityRepository<PlaceA>
 */
class PlaceARepository extends ServiceEntityRepository implements PlaceRepositoryInterface
{
    /**
     * PlaceARepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceA::class);
    }

    /**
     * Find city by given place.
     *
     * @param PlaceP $place
     * @return PlaceA|null
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function findCityByPlaceP(PlaceP $place): ?PlaceA
    {
        $countryCode = $place->getCountryCode();

        $queryBuilder = $this->createQueryBuilder('a');

        /* Country code */
        $queryBuilder->andWhere('a.countryCode = :cc')
            ->setParameter('cc', $countryCode);

        $queryBuilder->andWhere('a.featureClass = :fc')
            ->setParameter('fc', PlaceLoaderService::FEATURE_CLASS_A);

        switch ($countryCode) {
            case 'AT':
            case 'CH':
            case 'ES':
            case 'PL':
                $queryBuilder->andWhere('a.featureCode = :fco')
                    ->setParameter('fco', PlaceLoaderService::FEATURE_CODE_A_ADM3);
                $queryBuilder->andWhere('a.admin3Code = :ac')
                    ->setParameter('ac', $place->getAdmin3Code());
                break;

            /* de, etc. */
            default:
                $queryBuilder->andWhere('a.featureCode = :fco')
                    ->setParameter('fco', PlaceLoaderService::FEATURE_CODE_A_ADM4);
                $queryBuilder->andWhere('a.admin4Code = :ac')
                    ->setParameter('ac', $place->getAdmin4Code());
        }

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($result === null || $result instanceof PlaceA) {
            return $result;
        }

        throw new Exception(sprintf('Unexpected place instance (!PlaceA) (%s:%d).', __FILE__, __LINE__));
    }

    /**
     * Find state by given city.
     *
     * @param PlaceA $city
     * @return PlaceA|null
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function findStateByCity(PlaceA $city): ?PlaceA
    {
        $countryCode = $city->getCountryCode();

        $queryBuilder = $this->createQueryBuilder('a');

        /* Country code */
        $queryBuilder->andWhere('a.countryCode = :cc')
            ->setParameter('cc', $countryCode);

        $queryBuilder->andWhere('a.featureClass = :fc')
            ->setParameter('fc', PlaceLoaderService::FEATURE_CLASS_A);

        switch ($countryCode) {

            /* de, etc. */
            default:
                $queryBuilder->andWhere('a.featureCode = :fco')
                    ->setParameter('fco', PlaceLoaderService::FEATURE_CODE_A_ADM1);
                $queryBuilder->andWhere('a.admin1Code = :ac')
                    ->setParameter('ac', $city->getAdmin1Code());
        }

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($result === null || $result instanceof PlaceA) {
            return $result;
        }

        throw new Exception(sprintf('Unexpected place instance (!PlaceA) (%s:%d).', __FILE__, __LINE__));
    }
}
