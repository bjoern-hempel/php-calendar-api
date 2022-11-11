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
use App\Repository\ImageRepository;
use App\Security\Voter\UserVoter;
use App\Utils\FileNameConverter;
use App\Utils\GPSConverter;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class Image
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.3 (2022-07-16)
 * @since 0.1.3 (2022-11-11) PHPStan refactoring.
 * @since 0.1.2 (2022-07-16) Change self::$path to string|null.
 * @since 0.1.1 (2022-01-29) Possibility to disable the JWT locally for debugging processes (#45)
 * @since 0.1.0 First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    # Security filter for collection operations at App\Doctrine\CurrentUserExtension
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['image']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['image_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended Image resources.',
                'summary' => 'Retrieves the collection of extended Image resources.',
            ],
            'path' => '/images/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['image']],
            'security_post_denormalize' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_POST.'")',
            'security_post_denormalize_message' => "Only own images can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_DELETE.'", object.user)',
            'security_message' => 'Only own images can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_GET.'", object.user)',
            'security_message' => 'Only own images can be read.',
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['image_extended']],
            'openapi_context' => [
                'description' => 'Retrieves an extended Image resource.',
                'summary' => 'Retrieves an extended Image resource.',
            ],
            'path' => '/images/{id}/extended.{_format}',
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_GET.'", object.user)',
            'security_message' => 'Only own images can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_PATCH.'", object.user)',
            'security_message' => 'Only own images can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.UserVoter::ATTRIBUTE_IMAGE_PUT.'", object.user)',
            'security_message' => 'Only own images can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['image']],
    order: ['id' => 'ASC'],
)]
class Image implements EntityInterface
{
    use TimestampsTrait;

    public const CRUD_FIELDS_ADMIN = ['id', 'user'];

    public const CRUD_FIELDS_REGISTERED = ['id', 'user', 'name', 'path', 'pathSource', 'pathSourcePreview', 'width', 'height', 'size', 'title', 'latitude', 'longitude', 'url', 'gpsHeight', 'iso', 'mime', 'place', 'placeDistrict', 'placeCity', 'placeState', 'placeCountry', 'placeTimezone', 'information', 'takenAt', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'user', 'name', 'pathSourcePreview', 'width', 'height', 'size', 'title', 'latitude', 'longitude', 'information', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'user', 'path', 'title', 'url'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'user', 'path', 'width', 'height', 'size', 'title', 'latitude', 'longitude', 'url', 'gpsHeight', 'iso', 'mime', 'place', 'placeDistrict', 'placeCity', 'placeState', 'placeCountry', 'placeTimezone', 'information', 'takenAt', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_FILTER = ['user', 'width', 'height', 'size'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['image', 'image_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['image', 'image_extended'])]
    public ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['image', 'image_extended'])]
    private ?string $path = null;

    private ?string $pathSource = null;

    private ?string $pathTarget = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['image', 'image_extended'])]
    private ?int $width;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['image', 'image_extended'])]
    private ?int $height;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['image', 'image_extended'])]
    private ?int $size;

    /** @var Collection<int, CalendarImage> */
    #[ORM\OneToMany(mappedBy: 'image', targetEntity: CalendarImage::class, orphanRemoval: true)]
    #[Groups(['image', 'image_extended'])]
    private Collection $calendarImages;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['image_extended'])]
    private ?float $latitude;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['image_extended'])]
    private ?float $longitude;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $url = null;

    #[ORM\Column(name: 'gps_height', type: 'integer', nullable: true)]
    #[Groups(['image_extended'])]
    private ?int $gpsHeight;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['image_extended'])]
    private ?int $iso;

    #[ORM\Column(type: 'string', length: 63, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $mime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $place;

    #[ORM\Column(name: 'place_district', type: 'string', length: 255, nullable: true)]
    private ?string $placeDistrict;

    #[ORM\Column(name: 'place_city', type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $placeCity;

    #[ORM\Column(name: 'place_state', type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $placeState;

    #[ORM\Column(name: 'place_country', type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $placeCountry;

    #[ORM\Column(name: 'place_timezone', type: 'string', length: 255, nullable: true)]
    #[Groups(['image_extended'])]
    private ?string $placeTimezone;

    /** @var array<string, mixed> $information */
    #[ORM\Column(type: 'json')]
    #[Groups(['image_extended'])]
    private array $information = [];

    #[ORM\Column(name: 'taken_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $takenAt;

    public const PATH_TYPE_SOURCE = 'source';

    public const PATH_TYPE_TARGET = 'target';

    public const PATH_TYPE_EXPECTED = 'expected';

    public const PATH_TYPE_COMPARE = 'compare';

    public const PATH_TYPE_AUTO = 'auto';

    public const PATH_DATA = 'data';

    public const PATH_IMAGES = 'images';

    public const PATH_DATA_IMAGES = 'data/images';

    public const WIDTH_400 = 400;

    /**
     * Image constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->calendarImages = new ArrayCollection();
    }

    /**
     * __toString method.
     *
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Gets the name of the image.
     *
     * @return string
     * @throws Exception
     */
    public function getName(): string
    {
        if ($this->path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        $array = explode('/', $this->path);

        return end($array);
    }

    /**
     * Gets the id of this image.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the user of this image.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Gets the user id of this calendar.
     *
     * @return int|null
     * @throws Exception
     */
    #[Groups(['image', 'image_extended'])]
    public function getUserId(): ?int
    {
        return $this->getUser()?->getId();
    }

    /**
     * Sets the user of this image.
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
     * Gets the relative path of this image.
     *
     * @param string $type
     * @param bool $tmp
     * @param bool $test
     * @param string $outputMode
     * @param string $rootPath
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string|null
     * @throws Exception
     */
    public function getPath(string $type = self::PATH_TYPE_SOURCE, bool $tmp = false, bool $test = false, string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, string $rootPath = '', ?int $width = null, ?CalendarImage $calendarImage = null): ?string
    {
        if ($this->path === null) {
            return null;
        }

        $path = match (true) {
            $type === self::PATH_TYPE_SOURCE && $this->pathSource !== null => $this->pathSource,
            $type === self::PATH_TYPE_TARGET && $this->pathTarget !== null => $this->pathTarget,
            default => $this->path,
        };

        $fileNameConverter = new FileNameConverter($path, $rootPath, $test);

        return $fileNameConverter->getFilename(
            $type,
            $width,
            $tmp,
            $test,
            $outputMode,
            $calendarImage ? strval($calendarImage->getId()) : null
        );
    }

    /**
     * Gets the absolute path of this image.
     *
     * @param string $type
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathFull(string $type = self::PATH_TYPE_SOURCE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        $path = $this->getPath($type, $tmp, $test, FileNameConverter::MODE_OUTPUT_ABSOLUTE, $rootPath, $width, $calendarImage);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathSource(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        $path = $this->getPath(self::PATH_TYPE_SOURCE, $tmp, $test, $outputMode, $rootPath, $width, $calendarImage);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of this image (preview placeholder).
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathSourcePreview(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathSource($outputMode, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Gets the relative or absolute source path of this image with 400px width.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathSource400(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathSource($outputMode, $test, $rootPath, $tmp, self::WIDTH_400, $calendarImage);
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathTarget(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        $path = $this->getPath(self::PATH_TYPE_TARGET, $tmp, $test, $outputMode, $rootPath, $width, $calendarImage);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of this image (preview placeholder).
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathTargetPreview(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathTarget($outputMode, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Gets the relative or absolute source path of this image with 400px width.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathTarget400(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathTarget($outputMode, $test, $rootPath, $tmp, self::WIDTH_400, $calendarImage);
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathExpected(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        $path = $this->getPath(self::PATH_TYPE_EXPECTED, $tmp, $test, $outputMode, $rootPath, $width, $calendarImage);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param string $outputMode
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathCompare(string $outputMode = FileNameConverter::MODE_OUTPUT_FILE, bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        $path = $this->getPath(self::PATH_TYPE_COMPARE, $tmp, $test, $outputMode, $rootPath, $width, $calendarImage);

        if ($path === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }

    /**
     * Gets the absolute source path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathSourceFull(bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathSource(FileNameConverter::MODE_OUTPUT_ABSOLUTE, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Gets the absolute target path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathTargetFull(bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathTarget(FileNameConverter::MODE_OUTPUT_ABSOLUTE, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Gets the absolute source path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathExpectedFull(bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathExpected(FileNameConverter::MODE_OUTPUT_ABSOLUTE, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Gets the absolute target path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @param int|null $width
     * @param CalendarImage|null $calendarImage
     * @return string
     * @throws Exception
     */
    public function getPathCompareFull(bool $test = false, string $rootPath = '', bool $tmp = false, ?int $width = null, ?CalendarImage $calendarImage = null): string
    {
        return $this->getPathCompare(FileNameConverter::MODE_OUTPUT_ABSOLUTE, $test, $rootPath, $tmp, $width, $calendarImage);
    }

    /**
     * Sets the relative path of this image.
     *
     * @param string|null $path
     * @return $this
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sets the relative path of this image.
     *
     * @param string $pathSource
     * @return $this
     */
    public function setPathSource(string $pathSource): self
    {
        $this->pathSource = $pathSource;

        return $this;
    }

    /**
     * Sets the relative path of this image.
     *
     * @param string $pathTarget
     * @return $this
     */
    public function setPathTarget(string $pathTarget): self
    {
        $this->pathTarget = $pathTarget;

        return $this;
    }

    /**
     * Gets the width of this image.
     *
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Sets the width of this image.
     *
     * @param int|null $width
     * @return $this
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Gets the height of this image.
     *
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * Sets the height of this image.
     *
     * @param int|null $height
     * @return $this
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Gets the size of this image.
     *
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Sets the size of this image.
     *
     * @param int|null $size
     * @return $this
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets all related calendar images from this image.
     *
     * @return Collection<int, CalendarImage>
     */
    public function getCalendarImages(): Collection
    {
        return $this->calendarImages;
    }

    /**
     * Gets all related calendar images as simple id list.
     *
     * @return Collection<int, int>
     */
    #[Groups(['image', 'image_extended'])]
    public function getCalendarImageIds(): Collection
    {
        return $this->getCalendarImages()->map(function (CalendarImage $calendarImage) {
            return $calendarImage->getId();
        });
    }

    /**
     * Adds a related calendar image to this image.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     */
    public function addCalendarImage(CalendarImage $calendarImage): self
    {
        if (!$this->calendarImages->contains($calendarImage)) {
            $this->calendarImages[] = $calendarImage;
            $calendarImage->setImage($this);
        }

        return $this;
    }

    /**
     * Removes a given calendar image from this image.
     *
     * @param CalendarImage $calendarImage
     * @return $this
     * @throws Exception
     */
    public function removeCalendarImage(CalendarImage $calendarImage): self
    {
        if ($this->calendarImages->removeElement($calendarImage)) {
            // set the owning side to null (unless already changed)
            if ($calendarImage->getImage() === $this) {
                $calendarImage->setImage(null);
            }
        }

        return $this;
    }

    /**
     * Gets the latitude of this image.
     *
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * Sets the latitude of this image.
     *
     * @param float|null $latitude
     * @return $this
     */
    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Gets the longitude of this image.
     *
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude of this image.
     *
     * @param float|null $longitude
     * @return $this
     */
    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Returns the full position of this image.
     *
     * @return string
     * @throws Exception
     */
    public function getFullPosition(): string
    {
        if ($this->getLatitude() === null || $this->getLongitude() === null) {
            return '';
        }

        return sprintf(
            '%s %s',
            GPSConverter::decimalDegree2dms($this->getLatitude(), $this->getLatitude() < 0 ? GPSConverter::DIRECTION_SOUTH : GPSConverter::DIRECTION_NORTH),
            GPSConverter::decimalDegree2dms($this->getLongitude(), $this->getLongitude() < 0 ? GPSConverter::DIRECTION_WEST : GPSConverter::DIRECTION_EAST)
        );
    }

    /**
     * Gets the title of this image.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title of this image.
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
     * Gets the url of this image.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Sets the title of this image.
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
     * Gets the gps height of this image.
     *
     * @return int|null
     */
    public function getGpsHeight(): ?int
    {
        return $this->gpsHeight;
    }

    /**
     * Sets the gps height of this image.
     *
     * @param int|null $gpsHeight
     * @return $this
     */
    public function setGpsHeight(?int $gpsHeight): self
    {
        $this->gpsHeight = $gpsHeight;

        return $this;
    }

    /**
     * Gets the iso of this image.
     *
     * @return int|null
     */
    public function getIso(): ?int
    {
        return $this->iso;
    }

    /**
     * Sets the iso of this image.
     *
     * @param int|null $iso
     * @return $this
     */
    public function setIso(?int $iso): self
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * Gets the mime type of this image.
     *
     * @return string|null
     */
    public function getMime(): ?string
    {
        return $this->mime;
    }

    /**
     * Sets the mime type of this image.
     *
     * @param string|null $mime
     * @return $this
     */
    public function setMime(?string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Gets the place name of this image.
     *
     * @return string|null
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    /**
     * Sets the place name of this image.
     *
     * @param string|null $place
     * @return $this
     */
    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Gets the district name of this image.
     *
     * @return string|null
     */
    public function getPlaceDistrict(): ?string
    {
        return $this->placeDistrict;
    }

    /**
     * Sets the district name of this image.
     *
     * @param string|null $placeDistrict
     * @return $this
     */
    public function setPlaceDistrict(?string $placeDistrict): self
    {
        $this->placeDistrict = $placeDistrict;

        return $this;
    }

    /**
     * Gets the city of this image.
     *
     * @return string|null
     */
    public function getPlaceCity(): ?string
    {
        return $this->placeCity;
    }

    /**
     * Sets the city of this image.
     *
     * @param string|null $placeCity
     * @return $this
     */
    public function setPlaceCity(?string $placeCity): self
    {
        $this->placeCity = $placeCity;

        return $this;
    }

    /**
     * Gets the state of this image.
     *
     * @return string|null
     */
    public function getPlaceState(): ?string
    {
        return $this->placeState;
    }

    /**
     * Sets the state of this image.
     *
     * @param string|null $placeState
     * @return $this
     */
    public function setPlaceState(?string $placeState): self
    {
        $this->placeState = $placeState;

        return $this;
    }

    /**
     * Gets the country of this image.
     *
     * @return string|null
     */
    public function getPlaceCountry(): ?string
    {
        return $this->placeCountry;
    }

    /**
     * Sets the country of this image.
     *
     * @param string|null $placeCountry
     * @return $this
     */
    public function setPlaceCountry(?string $placeCountry): self
    {
        $this->placeCountry = $placeCountry;

        return $this;
    }

    /**
     * Gets the timezone of this image.
     *
     * @return string|null
     */
    public function getPlaceTimezone(): ?string
    {
        return $this->placeTimezone;
    }

    /**
     * Sets the timezone of this image.
     *
     * @param string|null $placeTimezone
     * @return $this
     */
    public function setPlaceTimezone(?string $placeTimezone): self
    {
        $this->placeTimezone = $placeTimezone;

        return $this;
    }

    /**
     * Gets the information of this image.
     *
     * @return array<string, mixed>|null
     */
    public function getInformation(): ?array
    {
        return $this->information;
    }

    /**
     * Sets the information of this image.
     *
     * @param array<string, mixed> $information
     * @return $this
     */
    public function setInformation(array $information): self
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Gets the time taken at of this image.
     *
     * @return DateTimeImmutable|null
     */
    public function getTakenAt(): ?DateTimeImmutable
    {
        return $this->takenAt;
    }

    /**
     * Sets the time taken at of this image.
     *
     * @param DateTimeImmutable|null $takenAt
     * @return $this
     */
    public function setTakenAt(?DateTimeImmutable $takenAt): self
    {
        $this->takenAt = $takenAt;

        return $this;
    }
}
