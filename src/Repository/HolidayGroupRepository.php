<?php declare(strict_types=1);

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
}
