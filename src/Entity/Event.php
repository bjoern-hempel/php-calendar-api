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
use App\Repository\EventRepository;
use App\Utils\ArrayToObject;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class Event
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: EventRepository::class)]
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
            'security_post_denormalize' => 'is_granted("'.self::ATTRIBUTE_EVENT_POST.'")',
            'security_post_denormalize_message' => "Only own events can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.self::ATTRIBUTE_EVENT_DELETE.'", object.user)',
            'security_message' => 'Only own events can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.self::ATTRIBUTE_EVENT_GET.'", object.user)',
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
            'security' => 'is_granted("'.self::ATTRIBUTE_EVENT_GET.'", object.user)',
            'security_message' => 'Only own events can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.self::ATTRIBUTE_EVENT_PATCH.'", object.user)',
            'security_message' => 'Only own events can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['event']],
            'security' => 'is_granted("'.self::ATTRIBUTE_EVENT_PUT.'", object.user)',
            'security_message' => 'Only own events can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['event']],
    order: ['id' => 'ASC'],
)]
class Event
{
    use TimestampsTrait;

    public const ATTRIBUTE_EVENT_DELETE = 'EVENT_DELETE';

    public const ATTRIBUTE_EVENT_GET = 'EVENT_GET';

    public const ATTRIBUTE_EVENT_PATCH = 'EVENT_PATCH';

    public const ATTRIBUTE_EVENT_POST = 'EVENT_POST';

    public const ATTRIBUTE_EVENT_PUT = 'EVENT_PUT';

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
    private array $config = [];

    private ArrayToObject $configObject;

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
}
