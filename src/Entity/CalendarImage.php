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
use App\Repository\CalendarImageRepository;
use App\Security\Voter\UserVoter;
use App\Utils\ArrayToObject;
use App\Utils\FileNameConverter;
use App\Utils\Traits\JsonHelper;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class CalendarImage
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.2 (2022-11-11)
 * @since 0.1.2 (2022-11-11) PHPStan refactoring.
 * @since 0.1.1 (2022-01-29) Possibility to disable the JWT locally for debugging processes (#45)
 * @since 0.1.0 First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarImageRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
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
class CalendarImage implements EntityInterface
{
    use TimestampsTrait;

    use JsonHelper;

    public const CRUD_FIELDS_ADMIN = ['id', 'user'];

    public const CRUD_FIELDS_REGISTERED = ['id', 'user', 'calendar', 'image', 'pathSource', 'pathSourcePreview', 'pathTarget', 'pathTargetPreview', 'year', 'month', 'title', 'position', 'url', 'configJson', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'user', 'calendar', 'pathSourcePreview', 'pathTargetPreview', 'year', 'month', 'title', 'position', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'user', 'calendar', 'image', 'year', 'month', 'title', 'position', 'url', 'configJson'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'user', 'calendar', 'pathSource', 'pathTarget', 'year', 'month', 'title', 'position', 'url', 'configJson', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_FILTER = [/* 'user', 'calendar', 'image', */ 'year', 'month', 'title', 'position', 'url', 'updatedAt', 'createdAt', ];

    public const QUALITY_TARGET = 50;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    public ?User $user;

    #[ORM\ManyToOne(targetEntity: Calendar::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private ?Calendar $calendar;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private ?Image $image;

    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $year;

    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    private int $month = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $position = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups('calendar_image_extended')]
    private ?string $url = null;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups('calendar_image_extended')]
    private array $config = [
        'valign' => 1,
    ];

    private ArrayToObject $configObject;

    /**
     * CalendarImage constructor.
     */
    public function __construct()
    {
        $this->year = intval(date('Y'));
    }

    /**
     * __toString method.
     *
     * @return string
     * @throws Exception
     */
    #[Pure]
    public function __toString(): string
    {
        return $this->getTitleName();
    }

    /**
     * Returns the name of this calendar image.
     *
     * @return string
     * @throws Exception
     */
    #[Pure]
    public function getTitleName(): string
    {
        return sprintf('%s (%s/%s)', $this->getCalendar()?->getName(), $this->getYear(), $this->getMonth());
    }

    /**
     * Gets the relative or absolute source path of the image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @return string
     * @throws Exception
     */
    public function getPathSource(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null): string
    {
        if ($this->getImage() === null) {
            throw new Exception(sprintf('No Image was found (%s:%d).', __FILE__, __LINE__));
        }

        $path = $this->getImage()->getPath(Image::PATH_TYPE_SOURCE, $tmp, $test, $outputMode, $rootPath, $width, $this);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of the image (preview placeholder).
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @return string
     * @throws Exception
     */
    public function getPathSourcePreview(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null): string
    {
        return $this->getPathSource($outputMode, $test, $rootPath, $tmp, $width);
    }

    /**
     * Gets the relative or absolute source path of the image with 400px width.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathSource400(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathSource($outputMode, $test, $rootPath, $tmp, Image::WIDTH_400);
    }

    /**
     * Gets the relative source path of the image with 400px width.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathSource400Relative(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathSource400(FileNameConverter::MODE_OUTPUT_RELATIVE, $test, $rootPath, $tmp);
    }

    /**
     * Gets the relative or absolute source path of the image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @return string
     * @throws Exception
     */
    public function getPathTarget(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null): string
    {
        if ($this->getImage() === null) {
            throw new Exception(sprintf('No Image was found (%s:%d).', __FILE__, __LINE__));
        }

        $path = $this->getImage()->getPath(Image::PATH_TYPE_TARGET, $tmp, $test, $outputMode, $rootPath, $width, $this);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of the image (preview placeholder).
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @return string
     * @throws Exception
     */
    public function getPathTargetPreview(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null): string
    {
        return $this->getPathTarget($outputMode, $test, $rootPath, $tmp, $width);
    }

    /**
     * Gets the relative source path of the image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @return string
     * @throws Exception
     */
    public function getPathTargetRelative(bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null): string
    {
        if ($this->getImage() === null) {
            throw new Exception(sprintf('No Image was found (%s:%d).', __FILE__, __LINE__));
        }

        $path = $this->getImage()->getPath(Image::PATH_TYPE_TARGET, $tmp, $test, FileNameConverter::MODE_OUTPUT_RELATIVE, $rootPath, $width, $this);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the file, relative or absolute source path of the image with 400px width.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathTarget400(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathTarget($outputMode, $test, $rootPath, $tmp, Image::WIDTH_400);
    }

    /**
     * Gets the relative source path of the image with 400px width.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathTarget400Relative(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathTarget400(FileNameConverter::MODE_OUTPUT_RELATIVE, $test, $rootPath, $tmp);
    }

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
     * Gets the user id of this calendar.
     *
     * @return int|null
     * @throws Exception
     */
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    public function getUserId(): ?int
    {
        return $this->getUser()->getId();
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
     * @return Calendar|null
     * @throws Exception
     */
    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    /**
     * Gets the calendar id of this calendar image.
     *
     * @return int|null
     * @throws Exception
     */
    #[Pure]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    public function getCalendarId(): ?int
    {
        return $this->getCalendar()?->getId();
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
     * @return Image|null
     * @throws Exception
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * Gets the image id of this calendar image.
     *
     * @return int|null
     * @throws Exception
     */
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    public function getImageId(): ?int
    {
        return $this->getImage()?->getId();
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
     * Gets the name of this calendar image.
     *
     * @return string
     * @throws Exception
     */
    #[Pure]
    #[Groups(['calendar_image', 'calendar_image_extended'])]
    public function getName(): string
    {
        return sprintf('%d - %d', $this->getMonth(), $this->getYear());
    }

    /**
     * Gets the config as array.
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
}
