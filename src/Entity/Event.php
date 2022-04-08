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
use App\Entity\Trait\TimestampsTrait;
use App\EventListener\Entity\UserListener;
use App\Repository\EventRepository;
use App\Security\Voter\UserVoter;
use App\Utils\ArrayToObject;
use App\Utils\Traits\JsonHelper;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class Event
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-01-29)
 * @since 1.0.1 Possibility to disable the JWT locally for debugging processes (#45)
 * @since 1.0.0 First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    # Security filter for collection operations at App\Doctrine\CurrentUserExtension
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['event']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['event_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended Event resources.',
                'summary' => 'Retrieves the collection of extended Event resources.',
            ],
            'path' => '/events/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['event']],
            'security_post_denormalize' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_POST.'")',
            'security_post_denormalize_message' => "Only own events can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_DELETE.'", object.user)',
            'security_message' => 'Only own events can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_GET.'", object.user)',
            'security_message' => 'Only own events can be read.',
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['event_extended']],
            'openapi_context' => [
                'description' => 'Retrieves an extended Event resource.',
                'summary' => 'Retrieves an extended Event resource.',
            ],
            'path' => '/events/{id}/extended.{_format}',
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_GET.'", object.user)',
            'security_message' => 'Only own events can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_PATCH.'", object.user)',
            'security_message' => 'Only own events can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_EVENT_PUT.'", object.user)',
            'security_message' => 'Only own events can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['event']],
    order: ['id' => 'ASC'],
)]
class Event implements EntityInterface
{
    use TimestampsTrait;

    use JsonHelper;

    public const CRUD_FIELDS_ADMIN = ['id', 'user'];

    public const CRUD_FIELDS_REGISTERED = ['id', 'name', 'type', 'user', 'date', 'yearly', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_INDEX = ['id', 'name', 'type', 'user', 'date', 'yearly', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_NEW = ['id', 'name', 'type', 'user', 'date', 'yearly', 'configJson'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'name', 'type', 'user', 'date', 'yearly', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_FILTER = ['name', 'type', 'user', 'date', 'yearly', 'updatedAt', 'createdAt'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['event', 'event_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('event')]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    public ?User $user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event', 'event_extended'])]
    private string $name;

    #[ORM\Column(type: 'integer')]
    #[Groups(['event', 'event_extended'])]
    private int $type;

    #[ORM\Column(type: 'date')]
    #[Groups(['event', 'event_extended'])]
    private DateTimeInterface $date;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups(['event', 'event_extended'])]
    private array $config = [
        'color' => '255,255,255,100',
    ];

    #[ORM\Column(type: 'boolean')]
    #[Groups(['event', 'event_extended'])]
    private bool $yearly = false;

    private ArrayToObject $configObject;

    /**
     * __toString method.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Gets the id of this event.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the user of this event.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Gets the user id of this event.
     *
     * @return int|null
     * @throws Exception
     */
    #[Pure]
    #[Groups(['event', 'event_extended'])]
    public function getUserId(): ?int
    {
        return $this->getUser()?->getId();
    }

    /**
     * Sets the user of this event.
     *
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the name of this event.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this event.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the type of this event.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Sets the type of this event.
     *
     * @param int $type
     * @return $this
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the date of this event.
     *
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Sets the date of this event.
     *
     * @param DateTimeInterface $date
     * @return $this
     */
    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets the config.
     *
     * @return array<string|int|float|bool>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Gets the config as object.
     *
     * @return ArrayToObject
     * @throws Exception
     */
    public function getConfigObject(): ArrayToObject
    {
        if (!isset($this->configObject)) {
            $this->configObject = new ArrayToObject($this->config);
        }

        return $this->configObject;
    }

    /**
     * Sets the config.
     *
     * @param array<string|int|float|bool> $config
     * @return $this
     * @throws Exception
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

        $this->configObject = new ArrayToObject($config);

        return $this;
    }

    /**
     * Gets the config element as JSON.
     *
     * @param bool $beautify
     * @return string
     * @throws Exception
     */
    public function getConfigJson(bool $beautify = true): string
    {
        return self::jsonEncode($this->config, $beautify, 2);
    }

    /**
     * Sets the config element from JSON.
     *
     * @param string $json
     * @return $this
     */
    public function setConfigJson(string $json): self
    {
        $this->config = self::jsonDecodeArray($json);

        return $this;
    }

    /**
     * Gets the config element as JSON.
     *
     * @param bool $beautify
     * @return string
     * @throws Exception
     */
    public function getConfigJsonRaw(bool $beautify = true): string
    {
        return $this->getConfigJson(false);
    }

    /**
     * Sets the config element from JSON.
     *
     * @param string $json
     * @return $this
     */
    public function setConfigJsonRaw(string $json): self
    {
        return $this->setConfigJson($json);
    }

    /**
     * Gets the yearly status of this holiday.
     *
     * @return bool|null
     */
    public function getYearly(): ?bool
    {
        return $this->yearly;
    }

    /**
     * Sets the yearly status from this holiday.
     *
     * @param bool $yearly
     * @return $this
     */
    public function setYearly(bool $yearly): self
    {
        $this->yearly = $yearly;

        return $this;
    }
}
