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

namespace App\Controller\Admin;

use App\Controller\Admin\Base\BaseCrudController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-10)
 * @package App\Controller\Admin
 */
class UserCrudController extends BaseCrudController
{
    protected UserPasswordHasherInterface $userPasswordHasher;

    /**
     * UserCrudController constructor.
     *
     * @param UserPasswordHasherInterface $imageProperty
     * @throws Exception
     */
    public function __construct(UserPasswordHasherInterface $imageProperty)
    {
        $this->userPasswordHasher = $imageProperty;

        parent::__construct();
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Returns the entity of this class.
     *
     * @return string
     */
    #[Pure]
    public function getEntity(): string
    {
        return self::getEntityFqcn();
    }

    /**
     * Changes the password of the given user.
     *
     * @param User $user
     */
    protected function changePassword(User $user): void
    {
        if ($user->getPlainPassword() !== User::PASSWORD_UNCHANGED) {
            $encodedPassword = $this->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);
        }
    }

    /**
     * Changes the id hash of the given user.
     *
     * @param User $user
     */
    protected function changeIdHash(User $user): void
    {
        if (empty($user->getIdHash())) {
            $user->setIdHash($user->getIdHashNew());
        }
    }

    /**
     * Overwrite persistEntity method.
     *
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     * @throws Exception
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            throw new Exception(sprintf('Unexpected entity class (%s:%d)', __FILE__, __LINE__));
        }

        $user = $entityInstance;

        $this->changePassword($user);
        $this->changeIdHash($user);

        parent::persistEntity($entityManager, $user);
    }

    /**
     * Overwrite updateEntity method.
     *
     * @param EntityManagerInterface $entityManager
     * @param User $entityInstance
     * @throws Exception
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            throw new Exception(sprintf('Unexpected entity class (%s:%d)', __FILE__, __LINE__));
        }

        $user = $entityInstance;

        $this->changePassword($user);
        $this->changeIdHash($user);

        parent::updateEntity($entityManager, $user);
    }

    /**
     * Encodes given plain password.
     *
     * @param User $user
     * @param string $plainPassword
     * @return string
     */
    private function encodePassword(User $user, string $plainPassword): string
    {
        return $this->userPasswordHasher->hashPassword($user, $plainPassword);
    }
}
