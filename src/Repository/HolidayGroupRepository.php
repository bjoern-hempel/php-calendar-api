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

use App\Entity\HolidayGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class HolidayGroupRepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Repository
 * @extends ServiceEntityRepository<HolidayGroup>
 *
 * @method HolidayGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method HolidayGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method HolidayGroup[]    findAll()
 * @method HolidayGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayGroupRepository extends ServiceEntityRepository
{
    /**
     * HolidayGroupRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HolidayGroup::class);
    }

    /**
     * Find one by name field.
     *
     * @param string $name
     * @return HolidayGroup|null
     * @throws Exception
     */
    public function findOneByName(string $name): ?HolidayGroup
    {
        $result = $this->createQueryBuilder('hg')
            ->andWhere('hg.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof HolidayGroup) {
            return $result;
        }

        if ($result !== null) {
            throw new Exception(sprintf('Unsupported type (%s:%d).', __FILE__, __LINE__));
        }

        return null;
    }

    /**
     * Find one by short name field.
     *
     * @param string $shortName
     * @return HolidayGroup|null
     * @throws Exception
     */
    public function findOneByShortName(string $shortName): ?HolidayGroup
    {
        $result = $this->createQueryBuilder('hg')
            ->andWhere('hg.nameShort = :val')
            ->setParameter('val', $shortName)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof HolidayGroup) {
            return $result;
        }

        if ($result !== null) {
            throw new Exception(sprintf('Unsupported type (%s:%d).', __FILE__, __LINE__));
        }

        return null;
    }
}
