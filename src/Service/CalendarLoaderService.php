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

namespace App\Service;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\Image;
use App\Entity\User;
use App\Repository\CalendarImageRepository;
use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CalendarLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-31)
 * @package App\Command
 */
class CalendarLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    protected User $user;

    protected Calendar $calendar;

    protected CalendarImage $calendarImage;

    protected Image $image;

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
     * Returns the DocumentRepository.
     *
     * @return UserRepository
     * @throws Exception
     */
    protected function getUserRepository(): UserRepository
    {
        $repository = $this->manager->getRepository(User::class);

        if (!$repository instanceof UserRepository) {
            throw new Exception('Error while getting UserRepository.');
        }

        return $repository;
    }

    /**
     * Returns the CalendarRepository.
     *
     * @return CalendarRepository
     * @throws Exception
     */
    protected function getCalendarRepository(): CalendarRepository
    {
        $repository = $this->manager->getRepository(Calendar::class);

        if (!$repository instanceof CalendarRepository) {
            throw new Exception('Error while getting CalendarRepository.');
        }

        return $repository;
    }

    /**
     * Returns the CalendarImageRepository.
     *
     * @return CalendarImageRepository
     * @throws Exception
     */
    protected function getCalendarImageRepository(): CalendarImageRepository
    {
        $repository = $this->manager->getRepository(CalendarImage::class);

        if (!$repository instanceof CalendarImageRepository) {
            throw new Exception('Error while getting CalendarImageRepository.');
        }

        return $repository;
    }

    /**
     * Clears all internal objects.
     */
    protected function clear(): void
    {
        unset($this->user);
        unset($this->calendar);
        unset($this->calendarImage);
        unset($this->image);
    }

    /**
     * Loads and returns calendar from user.
     *
     * @param string $email
     * @param string $name
     * @param int $year
     * @param int $month
     * @return CalendarImage
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendarImage(string $email, string $name, int $year, int $month): CalendarImage
    {
        /* Clears all objects */
        $this->clear();
        
        /* Load user */
        $user = $this->getUserRepository()->findOneByEmail($email);
        if ($user === null) {
            throw new Exception(sprintf('Unable to find user with email "%s".', $email));
        }
        $this->user = $user;

        /* Load calendar */
        $calendar = $this->getCalendarRepository()->findOneByName($user, $name);
        if ($calendar === null) {
            throw new Exception(sprintf('Unable to find calendar with name "%s".', $name));
        }
        $this->calendar = $calendar;

        /* Load calendar image */
        $calendarImage = $this->getCalendarImageRepository()->findOneByYearAndMonth($user, $calendar, $year, $month);
        if ($calendarImage === null) {
            throw new Exception(sprintf('Unable to find calendar image with year "%d" and month "%d".', $year, $month));
        }
        $this->calendarImage = $calendarImage;

        /* Load image */
        $this->image = $calendarImage->getImage();

        /* Returns the calendar image */
        return $this->getCalendarImage();
    }

    /**
     * Returns the user object.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Returns the calendar object.
     *
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    /**
     * Returns the calendar image object.
     *
     * @return CalendarImage
     */
    public function getCalendarImage(): CalendarImage
    {
        return $this->calendarImage;
    }

    /**
     * Returns the image object.
     *
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
