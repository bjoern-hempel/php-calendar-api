<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Entity class User
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    # Security filter for collection operations at App\Doctrine\CurrentUserExtension
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['user']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['user_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended User resources.',
                'summary' => 'Retrieves the collection of extended User resources.',
            ],
            'path' => '/users/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['user']],
            'security_post_denormalize' => 'is_granted("'.self::ROLE_ADMIN.'")',
            'security_post_denormalize_message' => "Only admins can add users.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.self::ATTRIBUTE_USER_DELETE.'", object)',
            'security_message' => 'Only own users can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.self::ATTRIBUTE_USER_GET.'", object)',
            'security_message' => 'Only own users can be read.',
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['user_extended']],
            'openapi_context' => [
                'description' => 'Retrieves an extended User resource.',
                'summary' => 'Retrieves an extended User resource.',
            ],
            'path' => '/users/{id}/extended.{_format}',
            'security' => 'is_granted("'.self::ATTRIBUTE_USER_GET.'", object)',
            'security_message' => 'Only own users can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.self::ATTRIBUTE_USER_PATCH.'", object)',
            'security_message' => 'Only own users can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.self::ATTRIBUTE_USER_PUT.'", object)',
            'security_message' => 'Only own users can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['user']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampsTrait;

    public const ATTRIBUTE_USER_DELETE = 'USER_DELETE';

    public const ATTRIBUTE_USER_GET = 'USER_GET';

    public const ATTRIBUTE_USER_PATCH = 'USER_PATCH';

    public const ATTRIBUTE_USER_POST = 'USER_POST';

    public const ATTRIBUTE_USER_PUT = 'USER_PUT';

    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user', 'user_extended'])]
    private int $id;

    #[ORM\Column(name: 'id_hash', type: 'string', length: 40, unique: true, nullable: false)]
    /** @phpstan-ignore-next-line → idHash must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?string $idHash = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user', 'user_extended'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['user', 'user_extended'])]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['user', 'user_extended'])]
    private ?string $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['user', 'user_extended'])]
    private ?string $lastname;

    /** @var string[] $roles */
    #[ORM\Column(type: 'json', nullable: false)]
    #[Groups(['user', 'user_extended'])]
    private array $roles = [];

    /** @var Collection<int, Event> $events */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Event::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[Groups('user_extended')]
    private Collection $events;

    /** @var Collection<int, Image> $images */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Image::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[Groups('user_extended')]
    #[ApiSubresource]
    private Collection $images;

    /** @var Collection<int, Calendar> $calendars */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Calendar::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[Groups('user_extended')]
    #[ApiSubresource]
    private Collection $calendars;

    /** @var Collection<int, CalendarImage> $calendarImages */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CalendarImage::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[ApiSubresource]
    private Collection $calendarImages;

    /**
     * User constructor
     */
    #[Pure]
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->calendars = new ArrayCollection();
        $this->calendarImages = new ArrayCollection();
    }

    /**
     * Gets the id of this user.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the hash id of this user.
     *
     * @return string|null
     */
    public function getIdHash(): ?string
    {
        return $this->idHash;
    }

    /**
     * Sets the hash id of this user.
     *
     * @param string $idHash
     * @return $this
     */
    public function setIdHash(string $idHash): self
    {
        $this->idHash = $idHash;

        return $this;
    }

    /**
     * Gets the email of this user.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email of this user.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the username of this user.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Sets the username of this user.
     *
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the password of this user.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets the password of this user.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the firstname of this user.
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Sets the firstname of this user.
     *
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Gets the lastname of this user.
     *
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Sets the lastname of this user.
     *
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Gets the roles of this user.
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        /* Guarantee every user at least has ROLE_USER */
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * Gets the roles of this user.
     *
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Gets all related events.
     *
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * Adds a related event.
     *
     * @param Event $event
     * @return $this
     */
    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    /**
     * Removes a related event.
     *
     * @param Event $event
     * @return $this
     */
    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Gets all related images.
     *
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * Adds a related image.
     *
     * @param Image $image
     * @return $this
     */
    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUser($this);
        }

        return $this;
    }

    /**
     * Removes a related image.
     *
     * @param Image $image
     * @return $this
     * @throws Exception
     */
    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Gets all related calendars.
     *
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    /**
     * Adds a related calendar.
     *
     * @param Calendar $calendar
     * @return $this
     */
    public function addCalendar(Calendar $calendar): self
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->setUser($this);
        }

        return $this;
    }

    /**
     * Removes a related calendar.
     *
     * @param Calendar $calendar
     * @return $this
     * @throws Exception
     */
    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getUser() === $this) {
                $calendar->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Gets all related calendar images.
     *
     * @return Collection<int, CalendarImage>
     */
    public function getCalendarImages(): Collection
    {
        return $this->calendarImages;
    }

    /**
     * Adds a related calendar image.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     */
    public function addCalendarImage(CalendarImage $calendarImage): self
    {
        if (!$this->calendarImages->contains($calendarImage)) {
            $this->calendarImages[] = $calendarImage;
            $calendarImage->setUser($this);
        }

        return $this;
    }

    /**
     * Removes a related calendar image.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     * @throws Exception
     */
    public function removeCalendarImage(CalendarImage $calendarImage): self
    {
        if ($this->calendarImages->removeElement($calendarImage)) {
            // set the owning side to null (unless already changed)
            if ($calendarImage->getUser() === $this) {
                $calendarImage->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Sets automatically the hash id of this user.
     *
     * @return $this
     */
    #[ORM\PrePersist]
    public function setIdHashAutomatically(): self
    {
        if ($this->idHash === null) {
            $this->setIdHash(sha1(sprintf('salt_%d_%d', rand(0, 999999999), rand(0, 999999999))));
        }

        return $this;
    }

    /**
     * Erase credentials.
     *
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Gets the user identifier.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
