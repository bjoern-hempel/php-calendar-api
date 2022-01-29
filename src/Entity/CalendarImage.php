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
use App\Repository\CalendarImageRepository;
use App\Security\Voter\UserVoter;
use App\Utils\ArrayToObject;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class CalendarImage
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-01-29)
 * @since 1.0.1 Possibility to disable the JWT locally for debugging processes (#45)
 * @since 1.0.0 First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarImageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    # Security filter for collection operations at App\Doctrine\CurrentUserExtension
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['calendar_image']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_image_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended CalendarImage resources.',
                'summary' => 'Retrieves the collection of extended CalendarImage resources.',
            ],
            'path' => '/calendar_images/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['calendar_image']],
            'security_post_denormalize' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_POST.'")',
            'security_post_denormalize_message' => "Only own calendar images can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['calendar_image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_DELETE.'", object.user)',
            'security_message' => 'Only own calendar images can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['calendar_image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_GET.'", object.user)',
            'security_message' => 'Only own calendar images can be read.',
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_image_extended']],
            'openapi_context' => [
                'description' => 'Retrieves an extended CalendarImage resource.',
                'summary' => 'Retrieves an extended CalendarImage resource.',
            ],
            'path' => '/calendar_images/{id}/extended.{_format}',
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_GET.'", object.user)',
            'security_message' => 'Only own calendar images can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['calendar_image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_PATCH.'", object.user)',
            'security_message' => 'Only own calendar images can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['calendar_image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_CALENDAR_IMAGE_PUT.'", object.user)',
            'security_message' => 'Only own calendar images can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['calendar_image']],
    order: ['id' => 'ASC'],
)]
class CalendarImage
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    public ?User $user;

    #[ORM\ManyToOne(targetEntity: Calendar::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    /** @phpstan-ignore-next-line → Calendar must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?Calendar $calendar;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    /** @phpstan-ignore-next-line → Image must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?Image $image;

    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $year;

    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $month;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $position;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $url;

    /** @var array<string|int|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups('calendar_image_extended')]
    private array $config = [];

    private ArrayToObject $configObject;

    /**
     * Gets the id of this calendar image.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the user of this calendar image.
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
     * Sets the user of this calendar image.
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
     * Gets the calendar of this calendar image.
     *
     * @return Calendar
     * @throws Exception
     */
    public function getCalendar(): Calendar
    {
        if (!isset($this->calendar)) {
            throw new Exception(sprintf('No calendar was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->calendar;
    }

    /**
     * Gets the calendar of this calendar image.
     *
     * @param Calendar|null $calendar
     * @return $this
     */
    public function setCalendar(?Calendar $calendar): self
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Gets the image of this calendar image.
     *
     * @return Image
     * @throws Exception
     */
    public function getImage(): Image
    {
        if (!isset($this->image)) {
            throw new Exception(sprintf('No image was configured (%s:%d)', __FILE__, __LINE__));
        }

        return $this->image;
    }

    /**
     * Sets the image of this calendar image.
     *
     * @param Image|null $image
     * @return $this
     */
    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the year of this calendar image.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Sets the year of this calendar image.
     *
     * @param int $year
     * @return $this
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Gets the month of this calendar image.
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Sets the month of this calendar image.
     *
     * @param int $month
     * @return $this
     */
    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Gets the title of this calendar image.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of this calendar image.
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
     * Gets the position of this calendar image.
     *
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * Sets the position of this calendar image.
     *
     * @param string|null $position
     * @return $this
     */
    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Gets the url of this calendar image.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Sets the url of this calendar image.
     *
     * @param string|null $url
     * @return $this
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets the config as array.
     *
     * @return array<string|int|bool>
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
     * @param array<string|int|bool> $config
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
