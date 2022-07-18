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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Trait\TimestampsTrait;
use App\Repository\PlaceARepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class Place A
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.0 (2022-05-20)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PlaceARepository::class)]
#[ORM\Index(columns: ["coordinate"], name: 'coordinate_place_a', flags: ["spatial"])]
#[ApiResource]
class PlaceA extends Place
{
    use TimestampsTrait;
}
