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
 * @version 1.0.1 (2022-01-29)
 * @since 1.0.1 Possibility to disable the JWT locally for debugging processes (#45)
 * @since 1.0.0 First version.
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

    public const CRUD_FIELDS_REGISTERED = ['id', 'user', 'path', 'pathSource', 'pathTarget', 'width', 'height', 'size', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'user', 'width', 'height', 'size', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'user', 'path'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'user', 'path', 'pathTarget', 'width', 'height', 'size', 'updatedAt', 'createdAt'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['image', 'image_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['image', 'image_extended'])]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    public ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['image', 'image_extended'])]
    private string $path;

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

    public const PATH_TYPE_SOURCE = 'source';

    public const PATH_TYPE_TARGET = 'target';

    public const PATH_TYPE_EXPECTED = 'expected';

    public const PATH_TYPE_COMPARE = 'compare';

    public const PATH_IMAGES = 'images';

    public const PATH_DATA = 'data';

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
     */
    public function __toString(): string
    {
        return $this->path;
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
     * @return string
     * @throws Exception
     */
    public function getPath(string $type = self::PATH_TYPE_SOURCE, bool $tmp = false): string
    {
        $path = match (true) {
            $type === self::PATH_TYPE_SOURCE && $this->pathSource !== null => $this->pathSource,
            $type === self::PATH_TYPE_TARGET && $this->pathTarget !== null => $this->pathTarget,
            default => $this->path,
        };

        if ($tmp) {
            $path = preg_replace('~\.([a-z]+)$~', '.tmp.$1', $path);

            if ($path === null) {
                throw new Exception(sprintf('Unable to replace path (%s:%d).', __FILE__, __LINE__));
            }
        }

        if ($type === self::PATH_TYPE_SOURCE) {
            return $path;
        }

        $replacedPath = preg_replace(sprintf('~(^[a-z0-9]{40,40}/)%s(/)~', self::PATH_TYPE_SOURCE), sprintf('$1%s$2', $type), $path);

        if (!is_string($replacedPath)) {
            throw new Exception(sprintf('Unexpected replaced path (%s:%d).', __FILE__, __LINE__));
        }

        return $replacedPath;
    }

    /**
     * Gets the absolute path of this image.
     *
     * @param string $type
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathFull(string $type = self::PATH_TYPE_SOURCE, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        $imagePath = sprintf($test ? '%s/tests/%s' : '%s/%s', self::PATH_DATA, self::PATH_IMAGES);

        return sprintf('%s/%s/%s', $rootPath, $imagePath, $this->getPath($type, $tmp));
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param bool $full
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathSource(bool $full = false, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        if ($full) {
            return $this->getPathFull(self::PATH_TYPE_SOURCE, $test, $rootPath, $tmp);
        }

        return $this->getPath(self::PATH_TYPE_SOURCE, $tmp);
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param bool $full
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathTarget(bool $full = false, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        if ($full) {
            return $this->getPathFull(self::PATH_TYPE_TARGET, $test, $rootPath, $tmp);
        }

        return $this->getPath(self::PATH_TYPE_TARGET, $tmp);
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param bool $full
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathExpected(bool $full = false, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        if ($full) {
            return $this->getPathFull(self::PATH_TYPE_EXPECTED, $test, $rootPath, $tmp);
        }

        return $this->getPath(self::PATH_TYPE_EXPECTED, $tmp);
    }

    /**
     * Gets the relative or absolute source path of this image.
     *
     * @param bool $full
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathCompare(bool $full = false, bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        if ($full) {
            return $this->getPathFull(self::PATH_TYPE_COMPARE, $test, $rootPath, $tmp);
        }

        return $this->getPath(self::PATH_TYPE_COMPARE, $tmp);
    }

    /**
     * Gets the absolute source path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathSourceFull(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathSource(true, $test, $rootPath, $tmp);
    }

    /**
     * Gets the absolute target path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathTargetFull(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathTarget(true, $test, $rootPath, $tmp);
    }

    /**
     * Gets the absolute source path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathExpectedFull(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathExpected(true, $test, $rootPath, $tmp);
    }

    /**
     * Gets the absolute target path of this image.
     *
     * @param bool $test
     * @param string $rootPath
     * @param bool $tmp
     * @return string
     * @throws Exception
     */
    public function getPathCompareFull(bool $test = false, string $rootPath = '', bool $tmp = false): string
    {
        return $this->getPathCompare(true, $test, $rootPath, $tmp);
    }

    /**
     * Sets the relative path of this image.
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
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
}
