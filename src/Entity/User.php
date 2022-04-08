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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\Trait\TimestampsTrait;
use App\EventListener\Entity\UserListener;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
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
#[ORM\EntityListeners([UserListener::class])]
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
            'security_post_denormalize' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_POST.'")',
            'security_post_denormalize_message' => "Only admins can add users.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_DELETE.'", object)',
            'security_message' => 'Only own users can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_GET.'", object)',
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
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_GET.'", object)',
            'security_message' => 'Only own users can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_PATCH.'", object)',
            'security_message' => 'Only own users can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['user']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_USER_PUT.'", object)',
            'security_message' => 'Only own users can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['user']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EntityInterface
{
    use TimestampsTrait;

    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const API_ENDPOINT_COLLECTION = '/api/v1/users';

    public const API_ENDPOINT_ITEM = '/api/v1/users/%d';

    public const PASSWORD_UNCHANGED = '**********';

    public const SHORT_HASH_LENGTH = 8;

    public const CRUD_FIELDS_ADMIN = ['id'];

    public const CRUD_FIELDS_REGISTERED = ['id', 'idHash', 'email', 'username', 'password', 'plainPassword', 'firstname', 'lastname', 'roles', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'idHash', 'email', 'username', 'password', 'firstname', 'lastname', 'roles', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'email', 'username', 'plainPassword', 'firstname', 'lastname', 'roles'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'idHash', 'email', 'username', 'password', 'firstname', 'lastname', 'roles', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_FILTER = ['email', 'username', 'firstname', 'lastname'];

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

    private string $plainPassword;

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
     * __toString method.
     *
     * @return string
     */
    #[Pure]
    public function __toString(): string
    {
        return $this->getFullName();
    }

    /**
     * Returns the full name of user.
     *
     * @param bool $withRole
     * @return string
     */
    public function getFullName(bool $withRole = false): string
    {
        return sprintf('%s %s', $this->firstname, $this->lastname);
    }

    /**
     * Returns the config of user.
     *
     * @return string[]
     * @throws Exception
     */
    #[ArrayShape(['fullName' => 'string', 'roleI18n' => 'string'])]
    public function getConfig(): array
    {
        $roleI18n = match (true) {
            in_array(User::ROLE_SUPER_ADMIN, $this->roles) => 'admin.user.fields.roles.entries.roleSuperAdmin',
            in_array(User::ROLE_ADMIN, $this->roles) => 'admin.user.fields.roles.entries.roleAdmin',
            in_array(User::ROLE_USER, $this->roles), $this->roles === [] => 'admin.user.fields.roles.entries.roleUser',
            default => throw new Exception(sprintf('Unknown role (%s:%d).', __FILE__, __LINE__)),
        };

        return [
            'fullName' => sprintf('%s %s', $this->firstname, $this->lastname),
            'roleI18n' => $roleI18n,
        ];
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
     * @return string
     */
    public function getIdHash(): string
    {
        return $this->idHash ?? $this->getIdHashNew();
    }

    /**
     * Gets the hash id of this user.
     *
     * @return string
     */
    public function getIdHashShort(): string
    {
        return substr($this->getIdHash(), 0, self::SHORT_HASH_LENGTH);
    }

    /**
     * Gets the hash id of this user.
     *
     * @return string
     */
    public function getIdHashNew(): string
    {
        return sha1(rand(1000000, 9999999).rand(1000000, 9999999));
    }

    /**
     * Sets the hash id of this user.
     *
     * @param string|null $idHash
     * @return $this
     */
    public function setIdHash(?string $idHash = null): self
    {
        $this->idHash = $idHash ?? $this->getIdHashNew();

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
     * Gets the plain password of this user.
     *
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword ?? self::PASSWORD_UNCHANGED;
    }

    /**
     * Sets the plain password of this user.
     *
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

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
