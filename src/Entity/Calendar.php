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
use App\Repository\CalendarRepository;
use App\Utils\ArrayToObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Entity class Calendar
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    # Security filter for collection operations at App\Doctrine\CurrentUserExtension
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['calendar']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended Calendar resources.',
                'summary' => 'Retrieves the collection of extended Calendar resources.',
            ],
            'path' => '/calendars/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['calendar']],
            'security_post_denormalize' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_POST.'")',
            'security_post_denormalize_message' => "Only own calendars can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['calendar']],
            'security' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_DELETE.'", object.user)',
            'security_message' => 'Only own calendars can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['calendar']],
            'security' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_GET.'", object.user)',
            'security_message' => 'Only own calendars can be read.',
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_extended']],
            'openapi_context' => [
                'description' => 'Retrieves an extended Calendar resource.',
                'summary' => 'Retrieves an extended Calendar resource.',
            ],
            'path' => '/calendars/{id}/extended.{_format}',
            'security' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_GET.'", object.user)',
            'security_message' => 'Only own calendars can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['calendar']],
            'security' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_PATCH.'", object.user)',
            'security_message' => 'Only own calendars can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['calendar']],
            'security' => 'is_granted("'.self::ATTRIBUTE_CALENDAR_PUT.'", object.user)',
            'security_message' => 'Only own calendars can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['calendar']],
    order: ['id' => 'ASC'],
)]
class Calendar
{
    use TimestampsTrait;

    public const ATTRIBUTE_CALENDAR_DELETE = 'CALENDAR_DELETE';

    public const ATTRIBUTE_CALENDAR_GET = 'CALENDAR_GET';

    public const ATTRIBUTE_CALENDAR_PATCH = 'CALENDAR_PATCH';

    public const ATTRIBUTE_CALENDAR_POST = 'CALENDAR_POST';

    public const ATTRIBUTE_CALENDAR_PUT = 'CALENDAR_PUT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('calendar')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'calendars')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups(['calendar_extended', 'calendar'])]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    public ?User $user;

    #[ORM\ManyToOne(targetEntity: CalendarStyle::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups(['calendar_extended', 'calendar'])]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?CalendarStyle $calendar_style;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['calendar_extended', 'calendar'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['calendar_extended', 'calendar'])]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['calendar_extended', 'calendar'])]
    private ?string $subtitle;

    #[ORM\ManyToOne(targetEntity: HolidayGroup::class)]
    #[Groups(['calendar_extended', 'calendar'])]
    private ?HolidayGroup $holiday_group;

    /** @var Collection<int, CalendarImage> $calendarImages  */
    #[ORM\OneToMany(mappedBy: 'calendar', targetEntity: CalendarImage::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    #[Groups('calendar_extended')]
    #[ApiSubresource]
    private Collection $calendarImages;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups(['calendar_extended', 'calendar'])]
    private array $config = [];

    private ArrayToObject $configObject;

    /**
     * Calendar constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->calendarImages = new ArrayCollection();
    }

    /**
     * Gets the id of this calendar.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the user of this calendar.
     *
     * @return User
     * @throws Exception
     */
    public function getUser(): User
    {
        if (!isset($this->user)) {
            throw new Exception(sprintf('No user was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->user;
    }

    /**
     * Sets the user of this calendar.
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
     * Gets the calendar style of this calendar.
     *
     * @return CalendarStyle|null
     */
    public function getCalendarStyle(): ?CalendarStyle
    {
        return $this->calendar_style;
    }

    /**
     * Sets the calendar style of this calendar.
     *
     * @param CalendarStyle|null $calendar_style
     * @return $this
     */
    public function setCalendarStyle(?CalendarStyle $calendar_style): self
    {
        $this->calendar_style = $calendar_style;

        return $this;
    }

    /**
     * Gets the name of this calendar.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this calendar.
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
     * Gets the title of this calendar.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of this calendar.
     *
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the subtitle of this calendar.
     *
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * Sets the subtitle of this calendar.
     *
     * @param string|null $subtitle
     * @return $this
     */
    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Gets the holiday group of this calendar.
     *
     * @return HolidayGroup|null
     */
    public function getHolidayGroup(): ?HolidayGroup
    {
        return $this->holiday_group;
    }

    /**
     * Sets the holiday group of this calendar.
     *
     * @param HolidayGroup|null $holiday_group
     * @return $this
     */
    public function setHolidayGroup(?HolidayGroup $holiday_group): self
    {
        $this->holiday_group = $holiday_group;

        return $this;
    }

    /**
     * Gets the calendar images of this calendar.
     *
     * @return Collection<int, CalendarImage>
     */
    public function getCalendarImages(): Collection
    {
        return $this->calendarImages;
    }

    /**
     * Adds a calendar image to this calendar.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     */
    public function addCalendarImage(CalendarImage $calendarImage): self
    {
        if (!$this->calendarImages->contains($calendarImage)) {
            $this->calendarImages[] = $calendarImage;
            $calendarImage->setCalendar($this);
        }

        return $this;
    }

    /**
     * Removes a calendar image from this calendar.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     * @throws Exception
     */
    public function removeCalendarImage(CalendarImage $calendarImage): self
    {
        if ($this->calendarImages->removeElement($calendarImage)) {
            // set the owning side to null (unless already changed)
            if ($calendarImage->getCalendar() === $this) {
                $calendarImage->setCalendar(null);
            }
        }

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
