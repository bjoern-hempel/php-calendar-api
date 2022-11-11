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

use App\Entity\PlaceV;
use App\Repository\Base\PlaceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PlaceVRepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-20)
 * @package App\Command
 * @extends ServiceEntityRepository<PlaceV>
 */
class PlaceVRepository extends ServiceEntityRepository implements PlaceRepositoryInterface
{
    /**
     * PlaceHRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceV::class);
    }

    /**
     * Get highest geoname id.
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getHighestGeonameId(): int
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('MAX(p.geonameId)');

        return intval($queryBuilder->getQuery()->getSingleScalarResult());
    }
}
