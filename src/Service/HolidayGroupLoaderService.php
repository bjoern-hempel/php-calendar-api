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

namespace App\Service;

use App\Entity\HolidayGroup;
use App\Repository\HolidayGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class HolidayGroupLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-02)
 * @package App\Command
 */
class HolidayGroupLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    protected HolidayGroup $holidayGroup;

    /**
     * Calendar constructor
     *
     * @param KernelInterface $appKernel
     * @param EntityManagerInterface $manager
     */
    public function __construct(KernelInterface $appKernel, EntityManagerInterface $manager)
    {
        $this->appKernel = $appKernel;

        $this->manager = $manager;
    }

    /**
     * Returns the HolidayGroupRepository.
     *
     * @return HolidayGroupRepository
     * @throws Exception
     */
    protected function getHolidayGroupRepository(): HolidayGroupRepository
    {
        $repository = $this->manager->getRepository(HolidayGroup::class);

        if (!$repository instanceof HolidayGroupRepository) {
            throw new Exception('Error while getting HolidayGroup.');
        }

        return $repository;
    }

    /**
     * Clears all internal objects.
     */
    protected function clear(): void
    {
        unset($this->holidayGroup);
    }

    /**
     * Loads and returns calendar from user.
     *
     * @param string $holidayGroupName
     * @return HolidayGroup
     * @throws Exception
     */
    public function loadHolidayGroup(string $holidayGroupName): HolidayGroup
    {
        /* Clears all objects */
        $this->clear();

        /* Load user */
        $holidayGroup = $this->getHolidayGroupRepository()->findOneByName($holidayGroupName);
        if ($holidayGroup === null) {
            throw new Exception(sprintf('Unable to find holiday group with name "%s".', $holidayGroupName));
        }
        $this->holidayGroup = $holidayGroup;

        /* Returns the holiday group */
        return $this->getHolidayGroup();
    }

    /**
     * Returns the holiday group object.
     *
     * @return HolidayGroup
     */
    public function getHolidayGroup(): HolidayGroup
    {
        return $this->holidayGroup;
    }
}
