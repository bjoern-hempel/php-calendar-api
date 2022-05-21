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

use App\Entity\PlaceP;
use App\Repository\Base\PlaceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PlacePRepository
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-20)
 * @package App\Command
 * @extends ServiceEntityRepository<PlaceP>
 */
class PlacePRepository extends ServiceEntityRepository implements PlaceRepositoryInterface
{
    /**
     * PlaceHRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceP::class);
    }
}
