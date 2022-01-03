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

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Entity class Image
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Image
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?User $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $path;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $width;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $height;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $size;

    /** @var Collection<int, CalendarImage> */
    #[ORM\OneToMany(mappedBy: 'image', targetEntity: CalendarImage::class, orphanRemoval: true)]
    private Collection $calendarImages;

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
     * @return User|null
     */
    public function getUser(): ?User
    {
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
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the relative source path of this image.
     *
     * @param string|null $source
     * @return string
     */
    public function getSourcePath(?string $source = null): string
    {
        if ($source === null) {
            return $this->getPath();
        }

        return str_replace('/', sprintf('/%s/', $source), $this->getPath());
    }

    /**
     * Gets the relative target path of this image.
     *
     * @param string|null $target
     * @return string
     */
    public function getTargetPath(?string $target = 'calendar'): string
    {
        if ($target === null) {
            return $this->getPath();
        }

        return str_replace('/', sprintf('/%s/', $target), $this->getPath());
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
