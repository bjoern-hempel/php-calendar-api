<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Repository;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * Class CalendarImageRepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Repository
 * @extends ServiceEntityRepository<CalendarImage>
 *
 * @method CalendarImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalendarImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalendarImage[]    findAll()
 * @method CalendarImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarImageRepository extends ServiceEntityRepository
{
    /**
     * CalendarImageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarImage::class);
    }

    /**
     * Find one by name field.
     *
     * @param User $user
     * @param Calendar $calendar
     * @param int $year
     * @param int $month
     * @return CalendarImage|null
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function findOneByYearAndMonth(User $user, Calendar $calendar, int $year, int $month): ?CalendarImage
    {
        $result = $this->createQueryBuilder('ci')
            ->where('ci.user = :user')
            ->andWhere('ci.calendar = :calendar')
            ->andWhere('ci.year = :year')
            ->andWhere('ci.month = :month')
            ->setParameter('user', $user)
            ->setParameter('calendar', $calendar)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof CalendarImage) {
            return $result;
        }

        if ($result !== null) {
            throw new Exception(sprintf('Unsupported type (%s:%d).', __FILE__, __LINE__));
        }

        return null;
    }
}
