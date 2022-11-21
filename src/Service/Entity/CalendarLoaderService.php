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

    protected UserLoaderService $userLoaderService;

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
     * @param UserLoaderService $userLoaderService
     */
    public function __construct(KernelInterface $appKernel, EntityManagerInterface $manager, SecurityService $securityService, UserLoaderService $userLoaderService)
    {
        $this->appKernel = $appKernel;

        $this->manager = $manager;

        $this->securityService = $securityService;

        $this->userLoaderService = $userLoaderService;
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
     * @param string|int $userEmailOrId
     * @param bool $clearObjects
     * @return User
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadUser(string|int $userEmailOrId, bool $clearObjects = true): User
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load user */
        $user = match (true) {
            is_int($userEmailOrId) => $this->getUserRepository()->find($userEmailOrId),
            is_string($userEmailOrId) => $this->getUserRepository()->findOneByEmail($userEmailOrId),
        };
        if ($user === null) {
            throw new Exception(sprintf('Unable to find user with email "%s".', $userEmailOrId));
        }
        $this->user = $user;

        return $this->getUser();
    }

    /**
     * Loads and returns the calendar by given email and calendar name
     *
     * @param string|int $userEmailOrId
     * @param string|int $calendarNameOrId
     * @param bool $clearObjects
     * @return Calendar
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendar(string|int $userEmailOrId, string|int $calendarNameOrId, bool $clearObjects = true): Calendar
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load User */
        $this->loadUser($userEmailOrId, false);

        /* Load calendar */
        $calendar = match (true) {
            is_int($calendarNameOrId) => $this->getCalendarRepository()->findOneByUserAndId($this->getUser(), $calendarNameOrId),
            is_string($calendarNameOrId) => $this->getCalendarRepository()->findOneByUserAndName($this->getUser(), $calendarNameOrId),
        };

        if ($calendar === null) {
            throw new Exception(sprintf('Unable to find calendar with name "%s".', $calendarNameOrId));
        }

        $this->calendar = $calendar;

        return $this->getCalendar();
    }

    /**
     * Loads and returns calendar from user and given calendar name.
     *
     * @param string|int $userEmailOrId
     * @param string|int $calendarNameOrId
     * @param int $year
     * @param int $month
     * @param bool $clearObjects
     * @return CalendarImage
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendarImageByCalendarNameYearAndMonth(string|int $userEmailOrId, string|int $calendarNameOrId, int $year, int $month, bool $clearObjects = true): CalendarImage
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Load calendar */
        $calendar = $this->loadCalendar($userEmailOrId, $calendarNameOrId, false);

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
     * Loads and returns calendar from user and given user hash, user and calendar image id.
     *
     * @param string $hash
     * @param int $userId
     * @param int $calendarImageId
     * @param bool $clearObjects
     * @return CalendarImage
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function loadCalendarImageByUserHashAndCalendarImage(string $hash, int $userId, int $calendarImageId, bool $clearObjects = true): CalendarImage
    {
        /* Clears all objects */
        if ($clearObjects) {
            $this->clear();
        }

        /* Check that user id and user hash matches. */
        $this->userLoaderService->loadUserCheckHash($userId, $hash);

        /* Load user. */
        $user = $this->getUserRepository()->find($userId);
        if (!$user instanceof User) {
            throw new Exception(sprintf('User not found (%s:%d).', __FILE__, __LINE__));
        }

        /* Load calendar image. */
        $calendarImage = $this->getCalendarImageRepository()->findOneBy([
            'user' => $user,
            'id' => $calendarImageId,
        ]);
        if (!$calendarImage instanceof CalendarImage) {
            throw new Exception(sprintf('CalendarImage not found (%s:%d).', __FILE__, __LINE__));
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

    /**
     * Returns a calendar given by id.
     *
     * @param int $id
     * @return Calendar
     * @throws Exception
     */
    public function findOneById(int $id): Calendar
    {
        $calendar = $this->getCalendarRepository()->findOneBy(['id' => $id]);

        if ($calendar === null) {
            throw new Exception(sprintf('Unable to find calendar with given id "%d" (%s:%d).', $id, __FILE__, __LINE__));
        }

        return $calendar;
    }
}
