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
use App\Repository\ImageRepository;
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
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
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
            'security_post_denormalize' => 'is_granted("'.self::ATTRIBUTE_IMAGE_POST.'")',
            'security_post_denormalize_message' => "Only own images can be added.",
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.self::ATTRIBUTE_IMAGE_DELETE.'", object.user)',
            'security_message' => 'Only own images can be deleted.',
        ],
        'get' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.self::ATTRIBUTE_IMAGE_GET.'", object.user)',
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
            'security' => 'is_granted("'.self::ATTRIBUTE_IMAGE_GET.'", object.user)',
            'security_message' => 'Only own images can be read.',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.self::ATTRIBUTE_IMAGE_PATCH.'", object.user)',
            'security_message' => 'Only own images can be modified.',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['image']],
            'security' => 'is_granted("'.self::ATTRIBUTE_IMAGE_PUT.'", object.user)',
            'security_message' => 'Only own images can be modified.',
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['image']],
    order: ['id' => 'ASC'],
)]
class Image
{
    use TimestampsTrait;

    public const ATTRIBUTE_IMAGE_DELETE = 'IMAGE_DELETE';

    public const ATTRIBUTE_IMAGE_GET = 'IMAGE_GET';

    public const ATTRIBUTE_IMAGE_PATCH = 'IMAGE_PATCH';

    public const ATTRIBUTE_IMAGE_POST = 'IMAGE_POST';

    public const ATTRIBUTE_IMAGE_PUT = 'IMAGE_PUT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['image', 'image_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['image', 'image_extended'])]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    public ?User $user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['image', 'image_extended'])]
    private string $path;

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

    /**
     * Image constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->calendarImages = new ArrayCollection();
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
     * @return string
     */
    public function getPath(string $type = self::PATH_TYPE_SOURCE): string
    {
        if ($type === self::PATH_TYPE_SOURCE) {
            return $this->path;
        }

        $pos = strpos($this->path, '/');

        $path = $pos !== false ? substr($this->path, $pos + 1) : $this->path;

        return sprintf('%s/%s', $type, $path);
    }

    /**
     * Gets the relative source path of this image.
     *
     * @return string
     */
    public function getPathSource(): string
    {
        return $this->getPath(self::PATH_TYPE_SOURCE);
    }

    /**
     * Gets the relative source path of this image.
     *
     * @return string
     */
    public function getPathTarget(): string
    {
        return $this->getPath(self::PATH_TYPE_TARGET);
    }

    /**
     * Gets the relative source path of this image.
     *
     * @return string
     */
    public function getPathExpected(): string
    {
        return $this->getPath(self::PATH_TYPE_EXPECTED);
    }

    /**
     * Gets the relative source path of this image.
     *
     * @return string
     */
    public function getPathCompare(): string
    {
        return $this->getPath(self::PATH_TYPE_COMPARE);
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
