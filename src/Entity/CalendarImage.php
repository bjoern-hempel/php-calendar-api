<?php declare(strict_types=1);

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

use App\Repository\CalendarImageRepository;
use App\Utils\ArrayToObject;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Entity class CalendarImage
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarImageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CalendarImage
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Calendar::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → Calendar must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?Calendar $calendar;

    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'calendarImages')]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → Image must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?Image $image;

    #[ORM\Column(type: 'integer')]
    private int $year;

    #[ORM\Column(type: 'integer')]
    private int $month;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $position;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $url;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $valign;

    /** @var array<string|int|bool> $config */
    #[ORM\Column(type: 'json')]
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
     * Gets the valign of this calendar image.
     *
     * @return int|null
     */
    public function getValign(): ?int
    {
        return $this->valign;
    }

    /**
     * Sets the valign of this calendar image.
     *
     * @param int|null $valign
     * @return $this
     */
    public function setValign(?int $valign): self
    {
        $this->valign = $valign;

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
