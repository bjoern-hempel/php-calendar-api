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

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Entity\Base\BaseLoaderService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class UserLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-15)
 * @package App\Command
 */
class UserLoaderService extends BaseLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    protected SecurityService $securityService;

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
    public function getUserRepository(): UserRepository
    {
        $repository = $this->manager->getRepository(User::class);

        if (!$repository instanceof UserRepository) {
            throw new Exception('Error while getting UserRepository.');
        }

        return $repository;
    }

    /**
     * Gets user entity by user id.
     *
     * @param int $userId
     * @return User
     * @throws Exception
     */
    protected function getUser(int $userId): User
    {
        $user = $this->getUserRepository()->find($userId);

        if ($user === null) {
            throw new Exception(sprintf('Unable to find user (%s:%d).', __FILE__, __LINE__));
        }

        return $user;
    }

    /**
     * Loads all users by permissions.
     *
     * @return User[]
     * @throws Exception
     */
    public function loadUsers(): array
    {
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $this->getUserRepository()->findAll();
        }

        return $this->getUserRepository()->findBy(['id' => $this->securityService->getUser()->getId()]);
    }

    /**
     * Load user by permissions.
     *
     * @param int $userId
     * @return User
     * @throws Exception
     */
    public function loadUser(int $userId): User
    {
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $this->getUser($userId);
        }

        if ($this->securityService->getUser()->getId() !== $userId) {
            throw new Exception(sprintf('Not allowed to get user with id %d.', $userId));
        }

        return $this->getUser($userId);
    }

    /**
     * Load user and check permission by given hash.
     *
     * @param int $userId
     * @param string $hash
     * @param bool $short
     * @return User
     * @throws Exception
     */
    public function loadUserCheckHash(int $userId, string $hash, bool $short = false): User
    {
        $user = $this->getUser($userId);

        $userHash = $short ? substr($user->getIdHash(), 0, 8) : $user->getIdHash();

        if ($userHash !== $hash) {
            throw new Exception(sprintf('The hash does not match with given hash "%s" (%s:%d).', $hash, __FILE__, __LINE__));
        }

        return $user;
    }
}
