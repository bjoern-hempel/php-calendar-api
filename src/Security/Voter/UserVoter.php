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

namespace App\Security\Voter;

use App\Doctrine\CurrentUserExtension;
use App\Entity\User;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-01-29)
 * @since 1.0.1 Possibility to disable the JWT locally for debugging processes (#45)
 * @since 1.0.0 First version.
 * @package App\Security\Voter
 * @see https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters
 */
class UserVoter extends Voter
{
    protected ParameterBagInterface $parameterBag;


    public const ATTRIBUTE_CALENDAR_DELETE = 'CALENDAR_DELETE';

    public const ATTRIBUTE_CALENDAR_GET = 'CALENDAR_GET';

    public const ATTRIBUTE_CALENDAR_PATCH = 'CALENDAR_PATCH';

    public const ATTRIBUTE_CALENDAR_POST = 'CALENDAR_POST';

    public const ATTRIBUTE_CALENDAR_PUT = 'CALENDAR_PUT';


    public const ATTRIBUTE_CALENDAR_IMAGE_DELETE = 'CALENDAR_IMAGE_DELETE';

    public const ATTRIBUTE_CALENDAR_IMAGE_GET = 'CALENDAR_IMAGE_GET';

    public const ATTRIBUTE_CALENDAR_IMAGE_PATCH = 'CALENDAR_IMAGE_PATCH';

    public const ATTRIBUTE_CALENDAR_IMAGE_POST = 'CALENDAR_IMAGE_POST';

    public const ATTRIBUTE_CALENDAR_IMAGE_PUT = 'CALENDAR_IMAGE_PUT';


    public const ATTRIBUTE_EVENT_DELETE = 'EVENT_DELETE';

    public const ATTRIBUTE_EVENT_GET = 'EVENT_GET';

    public const ATTRIBUTE_EVENT_PATCH = 'EVENT_PATCH';

    public const ATTRIBUTE_EVENT_POST = 'EVENT_POST';

    public const ATTRIBUTE_EVENT_PUT = 'EVENT_PUT';


    public const ATTRIBUTE_IMAGE_DELETE = 'IMAGE_DELETE';

    public const ATTRIBUTE_IMAGE_GET = 'IMAGE_GET';

    public const ATTRIBUTE_IMAGE_PATCH = 'IMAGE_PATCH';

    public const ATTRIBUTE_IMAGE_POST = 'IMAGE_POST';

    public const ATTRIBUTE_IMAGE_PUT = 'IMAGE_PUT';


    public const ATTRIBUTE_USER_DELETE = 'USER_DELETE';

    public const ATTRIBUTE_USER_GET = 'USER_GET';

    public const ATTRIBUTE_USER_PATCH = 'USER_PATCH';

    public const ATTRIBUTE_USER_POST = 'USER_POST';

    public const ATTRIBUTE_USER_PUT = 'USER_PUT';

    /**
     * UserVoter constructor.
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * Returns the allowed attributes.
     *
     * @return string[]
     */
    protected function getAllowedAttributes(): array
    {
        return [
            self::ATTRIBUTE_CALENDAR_DELETE,
            self::ATTRIBUTE_CALENDAR_GET,
            self::ATTRIBUTE_CALENDAR_PATCH,
            self::ATTRIBUTE_CALENDAR_POST,
            self::ATTRIBUTE_CALENDAR_PUT,

            self::ATTRIBUTE_CALENDAR_IMAGE_DELETE,
            self::ATTRIBUTE_CALENDAR_IMAGE_GET,
            self::ATTRIBUTE_CALENDAR_IMAGE_PATCH,
            self::ATTRIBUTE_CALENDAR_IMAGE_POST,
            self::ATTRIBUTE_CALENDAR_IMAGE_PUT,

            self::ATTRIBUTE_EVENT_DELETE,
            self::ATTRIBUTE_EVENT_GET,
            self::ATTRIBUTE_EVENT_PATCH,
            self::ATTRIBUTE_EVENT_POST,
            self::ATTRIBUTE_EVENT_PUT,

            self::ATTRIBUTE_IMAGE_DELETE,
            self::ATTRIBUTE_IMAGE_GET,
            self::ATTRIBUTE_IMAGE_PATCH,
            self::ATTRIBUTE_IMAGE_POST,
            self::ATTRIBUTE_IMAGE_PUT,

            self::ATTRIBUTE_USER_DELETE,
            self::ATTRIBUTE_USER_GET,
            self::ATTRIBUTE_USER_PATCH,
            self::ATTRIBUTE_USER_PUT,
        ];
    }

    /**
     * Checks if current $subject is supported to this class.
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     *
     * @see https://symfony.com/doc/current/security/voters.html
     * @see https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters
     */
    #[Pure]
    protected function supports(string $attribute, mixed $subject): bool
    {
        /* This voter is for User entities. */
        if (!$subject instanceof User) {
            return false;
        }

        /* Only the given attributes are supported. */
        if (!in_array($attribute, $this->getAllowedAttributes())) {
            return false;
        }

        return true;
    }

    /**
     * Checks that only the current user is allowed.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        /* JWT is disabled */
        if ($this->parameterBag->get(CurrentUserExtension::PARAMETER_NAME_JWT_ROLE) === AuthenticatedVoter::PUBLIC_ACCESS) {
            return true;
        }

        /* If the user is anonymous, do not grant access. */
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* Only own user is allowed. */
        return $user === $subject;
    }
}
