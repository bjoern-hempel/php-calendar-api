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
use App\Repository\UserRepository;
use App\Service\Entity\Base\BaseLoaderService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CalendarImageLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-18)
 * @package App\Command
 */
class CalendarImageLoaderService extends BaseLoaderService
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
     * Returns a calendar image given by id.
     *
     * @param int $id
     * @return CalendarImage
     * @throws Exception
     */
    public function findOneById(int $id): CalendarImage
    {
        $calendarImage = $this->getCalendarImageRepository()->findOneBy(['id' => $id]);

        if ($calendarImage === null) {
            throw new Exception(sprintf('Unable to find calendar image with given id "%d" (%s:%d).', $id, __FILE__, __LINE__));
        }

        return $calendarImage;
    }
}
