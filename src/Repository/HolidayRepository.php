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

use App\Command\CreateHolidayCommand;
use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class HolidayRepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2021-11-11) PHPStan refactoring.
 * @since 1.0.0 (2021-12-30) First version.
 * @since 1.0.1 (2022-11-11) PHPStan refactoring.
 * @extends ServiceEntityRepository<Holiday>
 *
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository extends ServiceEntityRepository
{
    /**
     * HolidayRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    /**
     * Find holiday by holiday group and date.
     *
     * @param HolidayGroup $holidayGroup
     * @param DateTime $date
     * @return Holiday[]
     * @throws Exception
     */
    public function findHolidaysByHolidayGroupAndDate(HolidayGroup $holidayGroup, DateTime $date): array
    {
        $holidays = $this->createQueryBuilder('h')
            ->andWhere('h.holidayGroup = :hg')
            ->andWhere('h.date = :date')
            ->setParameter('hg', $holidayGroup)
            ->setParameter('date', $date->format(CreateHolidayCommand::API_DATE_FORMAT))
            ->getQuery()
            ->getResult();

        if (!is_array($holidays)) {
            throw new Exception(sprintf('Unable to get holidays (%s:%d).', __FILE__, __LINE__));
        }

        foreach ($holidays as $holiday) {
            if (!$holiday instanceof Holiday) {
                throw new Exception(sprintf('Unexpected class (%s:%d).', __FILE__, __LINE__));
            }
        }

        return $holidays;
    }
}
