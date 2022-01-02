<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2022 Björn Hempel <bjoern@hempel.li>
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
