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

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\Event;
use App\Entity\Image;
use App\Entity\User;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-20)
 * @package App\Security\Voter
 * @see https://api-platform.com/docs/core/security/#hooking-custom-permission-checks-using-voters
 */
class UserVoter extends Voter
{
    /**
     * Returns the allowed attributes.
     *
     * @return string[]
     */
    protected function getAllowedAttributes(): array
    {
        return [
            Calendar::ATTRIBUTE_CALENDAR_DELETE,
            Calendar::ATTRIBUTE_CALENDAR_GET,
            Calendar::ATTRIBUTE_CALENDAR_PATCH,
            Calendar::ATTRIBUTE_CALENDAR_POST,
            Calendar::ATTRIBUTE_CALENDAR_PUT,

            CalendarImage::ATTRIBUTE_CALENDAR_IMAGE_DELETE,
            CalendarImage::ATTRIBUTE_CALENDAR_IMAGE_GET,
            CalendarImage::ATTRIBUTE_CALENDAR_IMAGE_PATCH,
            CalendarImage::ATTRIBUTE_CALENDAR_IMAGE_POST,
            CalendarImage::ATTRIBUTE_CALENDAR_IMAGE_PUT,

            Event::ATTRIBUTE_EVENT_DELETE,
            Event::ATTRIBUTE_EVENT_GET,
            Event::ATTRIBUTE_EVENT_PATCH,
            Event::ATTRIBUTE_EVENT_POST,
            Event::ATTRIBUTE_EVENT_PUT,

            Image::ATTRIBUTE_IMAGE_DELETE,
            Image::ATTRIBUTE_IMAGE_GET,
            Image::ATTRIBUTE_IMAGE_PATCH,
            Image::ATTRIBUTE_IMAGE_POST,
            Image::ATTRIBUTE_IMAGE_PUT,

            User::ATTRIBUTE_USER_DELETE,
            User::ATTRIBUTE_USER_GET,
            User::ATTRIBUTE_USER_PATCH,
            User::ATTRIBUTE_USER_PUT,
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

        /* If the user is anonymous, do not grant access. */
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* Only own user is allowed. */
        return $user === $subject;
    }
}
