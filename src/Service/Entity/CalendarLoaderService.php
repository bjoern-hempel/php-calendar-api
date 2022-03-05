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

namespace App\Service\Entity;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\EntityInterface;
use App\Entity\Image;
use App\Entity\User;
use App\Repository\CalendarImageRepository;
use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use App\Service\Entity\Base\BaseLoaderService;
use App\Service\SecurityService;
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
class CalendarLoaderService extends BaseLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    protected SecurityService $securityService;

    protected CalendarLoaderService $calendarLoaderService;

    protected User $user;

    protected Calendar $calendar;

    protected CalendarImage $calendarImage;

    protected Image $image;

    /**
     * Calendar constructor
     *
     * @param KernelInterface $appKernel
     * @param EntityManagerInterface $manager
     * @param SecurityService $securityService
     */
    public function __construct(KernelInterface $appKernel, EntityManagerInterface $manager, SecurityService $securityService)
    {
        $this->appKernel = $appKernel;

        $this->manager = $manager;

        $this->securityService = $securityService;
    }

    /**
     * Returns the UserRepository.
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
     * Loads all calendars.
     *
     * @return Calendar[]
     * @throws Exception
     */
    public function loadCalendars(): array
    {
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $this->getCalendarRepository()->findAll();
        }

        return $this->getCalendarRepository()->findBy(['user' => $this->securityService->getUser()]);
    }

    /**
     * Loads and returns user
     *
     * @param string $email
     * @param bool $clearObjects
     * @return User
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadUser(string $email, bool $clearObjects = true): User
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load user */
        $user = $this->getUserRepository()->findOneByEmail($email);
        if ($user === null) {
            throw new Exception(sprintf('Unable to find user with email "%s".', $email));
        }
        $this->user = $user;

        return $this->getUser();
    }

    /**
     * Loads and returns the calendar
     *
     * @param string $email
     * @param string $calendarName
     * @param bool $clearObjects
     * @return Calendar
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendar(string $email, string $calendarName, bool $clearObjects = true): Calendar
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load User */
        $this->loadUser($email, false);

        /* Load calendar */
        $calendar = $this->getCalendarRepository()->findOneByName($this->getUser(), $calendarName);
        if ($calendar === null) {
            throw new Exception(sprintf('Unable to find calendar with name "%s".', $calendarName));
        }
        $this->calendar = $calendar;

        return $this->getCalendar();
    }

    /**
     * Loads and returns calendar from user.
     *
     * @param string $email
     * @param string $calendarName
     * @param int $year
     * @param int $month
     * @param bool $clearObjects
     * @return CalendarImage
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendarImage(string $email, string $calendarName, int $year, int $month, bool $clearObjects = true): CalendarImage
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load user */
        $calendar = $this->loadCalendar($email, $calendarName, false);

        /* Load calendar image */
        $calendarImage = $this->getCalendarImageRepository()->findOneByYearAndMonth($this->getUser(), $calendar, $year, $month);
        if ($calendarImage === null) {
            throw new Exception(sprintf('Unable to find calendar image with year "%d" and month "%d".', $year, $month));
        }
        $this->calendarImage = $calendarImage;

        /* Load image */
        $image = $this->calendarImage->getImage();
        if ($image === null) {
            throw new Exception(sprintf('Image not found (%s:%d).', __FILE__, __LINE__));
        }
        $this->image = $image;

        /* Returns the calendar image */
        return $this->getCalendarImage();
    }

    /**
     * Returns the user object.
     *
     * @return User
     * @throws Exception
     */
    public function getUser(): User
    {
        if (!isset($this->user)) {
            throw new Exception(sprintf('No user was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->user;
    }

    /**
     * Returns the calendar object.
     *
     * @return Calendar
     * @throws Exception
     */
    public function getCalendar(): Calendar
    {
        if (!isset($this->calendar)) {
            throw new Exception(sprintf('No calendar was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->calendar;
    }

    /**
     * Returns the calendar image object.
     *
     * @return CalendarImage
     * @throws Exception
     */
    public function getCalendarImage(): CalendarImage
    {
        if (!isset($this->calendarImage)) {
            throw new Exception(sprintf('No calendar image was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->calendarImage;
    }

    /**
     * Returns the image object.
     *
     * @return Image
     * @throws Exception
     */
    public function getImage(): Image
    {
        if (!isset($this->image)) {
            throw new Exception(sprintf('No image was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->image;
    }
}
