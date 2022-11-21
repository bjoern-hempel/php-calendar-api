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

use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class SecurityService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-26)
 * @package App\Service
 */
class SecurityService
{
    protected Security $security;

    /**
     * SecurityService constructor
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Check if Security class is loaded and user is logged in.
     *
     * @return bool
     */
    public function isLoaded(): bool
    {
        return $this->security->getToken() instanceof TokenInterface;
    }

    /**
     * Returns the Security class.
     *
     * @return Security
     */
    public function getSecurity(): Security
    {
        return $this->security;
    }

    /**
     * Returns if User is logged in.
     *
     * @return bool
     */
    public function isUserLoggedIn(): bool
    {
        return $this->security->getUser() instanceof User;
    }

    /**
     * Returns User entity.
     *
     * @return User
     * @throws Exception
     */
    public function getUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new Exception(sprintf('Expect user class (%s:%d)', __FILE__, __LINE__));
        }

        return $user;
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied subject.
     *
     * @param string $attribute
     * @param mixed|null $subject
     * @return bool
     */
    public function isGranted(string $attribute, mixed $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }

    /**
     * Checks if user is an admin.
     *
     * @return bool
     */
    public function isGrantedByAnAdmin(): bool
    {
        return $this->isGrantedOr(User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN);
    }

    /**
     * Checks that at least one given role is granted.
     *
     * @return bool
     */
    public function isGrantedOr(): bool
    {
        /** @var string[] $attributes */
        $attributes = func_get_args();

        foreach ($attributes as $attribute) {
            if ($this->isGranted($attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks that all given roles are granted.
     *
     * @return bool
     */
    public function isGrantedAnd(): bool
    {
        /** @var string[] $attributes */
        $attributes = func_get_args();

        foreach ($attributes as $attribute) {
            if (!$this->isGranted($attribute)) {
                return false;
            }
        }

        return true;
    }
}
