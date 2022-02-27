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

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\EntityInterface;
use App\Entity\Event;
use App\Entity\Image;
use App\Entity\User;
use App\Service\SecurityService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class UserListener
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-26)
 * @package App\EventListener\Entity
 */
class UserListener
{
    protected SecurityService $securityService;

    /**
     * UserListener constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
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
        /* Check if we do have a logged-in user. */
        if (!$this->securityService->isUserLoggedIn()) {
            return;
        }

        /* Check permission. */
        switch (true) {
            /* Check indirect User. */
            case $entity instanceof Calendar:
            case $entity instanceof CalendarImage:
            case $entity instanceof Event:
                if (!$this->securityService->isGrantedByAnAdmin()) {
                    $entity->setUser($this->securityService->getUser());
                }

                if ($entity->getUser() === null) {
                    throw new Exception(sprintf('No user was given (%s:%d).', __LINE__, __FILE__));
                }
                break;

            /* Check indirect User. */
            case $entity instanceof Image:
                if ($entity->getUser() === null) {
                    throw new Exception(sprintf('No user was given (%s:%d).', __LINE__, __FILE__));
                }
                break;
        }
    }

    /**
     * Post persist.
     *
     * @param EntityInterface $entity
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    #[ORM\PostPersist]
    public function postPersistHandler(EntityInterface $entity, LifecycleEventArgs $event): void
    {
        /* Check if we do have a logged-in user. */
        if (!$this->securityService->isUserLoggedIn()) {
            return;
        }

        /* Check permission. */
        switch (true) {
            /* Check indirect User. */
            case $entity instanceof Calendar:
            case $entity instanceof CalendarImage:
            case $entity instanceof Event:
            case $entity instanceof Image:
                if ($entity->getUser() === null) {
                    throw new Exception(sprintf('No user was given (%s:%d).', __LINE__, __FILE__));
                }
                break;
        }
    }

    /**
     * Check permissions.
     *
     * @param EntityInterface $entity
     * @param LifecycleEventArgs $event
     * @throws AccessDeniedHttpException|Exception
     */
    #[ORM\PostLoad]
    public function postLoadHandler(EntityInterface $entity, LifecycleEventArgs $event): void
    {
        /* TODO: This happens, if the own user is loaded, before the Security class was set.
         * TODO: The second query in the actual list comes from Doctrine unfortunately cached. :(
         * TODO: We need to find a way to fix this.
         */
        if (!$this->securityService->isLoaded()) {
            return;
        }

        /* Admins can see all entities. */
        if ($this->securityService->isGrantedByAnAdmin()) {
            return;
        }

        /* Check permission. */
        switch (true) {
            /* Check direct User. */
            case $entity instanceof User:
                if ($entity !== $this->securityService->getUser()) {
                    throw new AccessDeniedHttpException(sprintf('You do not have permissions to see the User entity (%s:%d).', __FILE__, __LINE__));
                }
                break;

            /* Check indirect User. */
            case $entity instanceof Calendar:
            case $entity instanceof CalendarImage:
            case $entity instanceof Event:
            case $entity instanceof Image:
                if ($entity->getUser() !== $this->securityService->getUser()) {
                    throw new AccessDeniedHttpException(sprintf('You do not have permissions to see that entity (%s:%d).', __FILE__, __LINE__));
                }
                break;
        }
    }
}
