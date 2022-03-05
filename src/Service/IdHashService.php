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

use App\Entity\Image;
use App\Entity\User;
use App\Service\Entity\UserLoaderService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class IdHashService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-27)
 * @package App\Service
 */
class IdHashService
{
    protected SecurityService $securityService;

    protected RequestStack $requestStack;

    protected UserLoaderService $userLoaderService;

    /**
     * IdHashService constructor
     *
     * @param SecurityService $securityService
     * @param RequestStack $requestStack
     * @param UserLoaderService $userLoaderService
     */
    public function __construct(SecurityService $securityService, RequestStack $requestStack, UserLoaderService $userLoaderService)
    {
        $this->securityService = $securityService;

        $this->requestStack = $requestStack;

        $this->userLoaderService = $userLoaderService;
    }

    /**
     * Returns current request.
     *
     * @return Request
     * @throws Exception
     */
    protected function getCurrentRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        return $request;
    }

    /**
     * Get entity fqcn from POST request or entity dto.
     *
     * @return bool
     * @throws Exception
     */
    protected function isImagePosted(): bool
    {
        $request = $this->getCurrentRequest();

        if ($request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        if ($request->get('Image') === null) {
            return false;
        }

        return true;
    }

    /**
     * Returns the user by request.
     *
     * @return User
     * @throws Exception
     */
    public function getUserByRequest(): User
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new Exception(sprintf('No request stack was found (%s:%d).', __FILE__, __LINE__));
        }

        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new Exception(sprintf('Only POST request data are allowed (%s:%d).', __FILE__, __LINE__));
        }

        if ($request->get('Image') === null) {
            throw new Exception(sprintf('Unsupported POST data - Image block is missing (%s:%d).', __FILE__, __LINE__));
        }

        $image = $request->get('Image');

        if (!is_array($image)) {
            throw new Exception(sprintf('Unsupported POST data - Image block is not an array (%s:%d).', __FILE__, __LINE__));
        }

        if (!array_key_exists('user', $image)) {
            throw new Exception(sprintf('A posted user was expected (%s:%d).', __FILE__, __LINE__));
        }

        $user = $this->userLoaderService->getUserRepository()->find($image['user']);

        if ($user === null) {
            throw new Exception(sprintf('The user was found within the db (%s:%d).', __FILE__, __LINE__));
        }

        return $user;
    }

    /**
     * Returns User instance by given entity instance.
     *
     * @param object|null $entityInstance
     * @return User|null
     * @throws Exception
     */
    public function getUserByEntityInstance(?object $entityInstance): ?User
    {
        if ($entityInstance === null) {
            return null;
        }

        if (!$entityInstance instanceof Image) {
            throw new Exception(sprintf('Only Image instance is allowed (%s:%d).', __FILE__, __LINE__));
        }

        $image = $entityInstance;

        if ($image->getUser() === null) {
            return null;
        }

        return $image->getUser();
    }

    /**
     * Returns User from POST data or given entity instance.
     *
     * @param object|null $entityInstance
     * @return User
     * @throws Exception
     */
    public function getUser(?object $entityInstance): User
    {
        $user = null;

        /* 1) Only Admins can post user (security reason). */
        if ($user === null && $this->isImagePosted() && $this->securityService->isGrantedByAnAdmin()) {
            $user = $this->getUserByRequest();
        }

        /* 2) Try to get user from entity instance. */
        if ($user === null) {
            $user = $this->getUserByEntityInstance($entityInstance);
        }

        /* 3) Use the User from login. */
        if ($user === null && $this->securityService->isUserLoggedIn()) {
            $user = $this->securityService->getUser();
        }

        /* 4) Unable to get a user. */
        if ($user === null) {
            throw new Exception(sprintf('Unable to get user from POST data or given entity instance (%s:%d).', __FILE__, __LINE__));
        }

        /* Do not check the user for administrative authorized persons.  */
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $user;
        }

        /* Check the user for basic user. */
        if ($user !== $this->securityService->getUser()) {
            throw new Exception(sprintf('It is not allowed to use that User (%s:%d).', __FILE__, __LINE__));
        }

        return $user;
    }

    /**
     * Returns id hash.
     *
     * @param object|null $entityInstance
     * @return string
     * @throws Exception
     */
    public function getIdHash(?object $entityInstance): string
    {
        return $this->getUser($entityInstance)->getIdHash();
    }
}
