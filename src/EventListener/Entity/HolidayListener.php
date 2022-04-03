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

namespace App\EventListener\Entity;

use App\Entity\EntityInterface;
use App\Entity\Holiday;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;

/**
 * Class HolidayListener
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-03)
 * @package App\EventListener\Entity
 */
class HolidayListener
{
    /**
     * Replaces the year with 1970.
     *
     * @param Holiday $holiday
     * @return void
     * @throws Exception
     */
    protected function setDate(Holiday $holiday): void
    {
        if (!$holiday->getYearly()) {
            return;
        }

        $day = $holiday->getDate()->format('d');
        $month = $holiday->getDate()->format('m');
        $year = 1970;

        $dateString = sprintf('%d-%s-%s', $year, $month, $day);

        $date = DateTime::createFromFormat('Y-m-d', $dateString);

        if (!$date instanceof DateTime) {
            throw new Exception(sprintf('Unable to parse given date (%s:%d).', __FILE__, __LINE__));
        }

        $holiday->setDate($date);
    }

    /**
     * Pre persist.
     *
     * @param EntityInterface $entity
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersistHandler(EntityInterface $entity, LifecycleEventArgs $event): void
    {
        if (!$entity instanceof Holiday) {
            return;
        }

        $this->setDate($entity);
    }

    /**
     * Pre update.
     *
     * @param EntityInterface $entity
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function preUpdateHandler(EntityInterface $entity, LifecycleEventArgs $event): void
    {
        if (!$entity instanceof Holiday) {
            return;
        }

        $this->setDate($entity);
    }
}
