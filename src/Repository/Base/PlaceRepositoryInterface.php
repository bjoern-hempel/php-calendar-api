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

namespace App\Repository\Base;

use App\Entity\PlaceA;
use App\Entity\PlaceH;
use App\Entity\PlaceL;
use App\Entity\PlaceP;
use App\Entity\PlaceR;
use App\Entity\PlaceS;
use App\Entity\PlaceT;
use App\Entity\PlaceU;
use App\Entity\PlaceV;

/**
 * Interface PlaceRepositoryInterface
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-21)
 * @package App\Command
 */
interface PlaceRepositoryInterface
{
    /**
     * Returns one by.
     *
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return PlaceA|PlaceH|PlaceL|PlaceP|PlaceR|PlaceS|PlaceT|PlaceU|PlaceV|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null);
}
