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

use App\Repository\CalendarRepository;
use App\Utils\ArrayToObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Entity class Calendar
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Calendar
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'calendars')]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: CalendarStyle::class)]
    #[ORM\JoinColumn(nullable: false)]
    /** @phpstan-ignore-next-line → User must be nullable, but PHPStan checks ORM\JoinColumn(nullable: false) */
    private ?CalendarStyle $calendar_style;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $subtitle;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $background_color;

    #[ORM\Column(type: 'boolean')]
    private bool $print_calendar_week;

    #[ORM\Column(type: 'boolean')]
    private bool $print_week_number;

    #[ORM\Column(type: 'boolean')]
    private bool $print_qr_code_month;

    #[ORM\Column(type: 'boolean')]
    private bool $print_qr_code_title;

    #[ORM\ManyToOne(targetEntity: HolidayGroup::class)]
    private ?HolidayGroup $holiday_group;

    /** @var Collection<int, CalendarImage> $calendarImages  */
    #[ORM\OneToMany(mappedBy: 'calendar', targetEntity: CalendarImage::class, orphanRemoval: true)]
    private Collection $calendarImages;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
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
     * Gets the color of this calendar.
     *
     * @return string|null
     */
    public function getBackgroundColor(): ?string
    {
        return $this->background_color;
    }

    /**
     * Sets the name of this calendar.
     *
     * @param string|null $background_color
     * @return $this
     */
    public function setBackgroundColor(?string $background_color): self
    {
        $this->background_color = $background_color;

        return $this;
    }

    /**
     * Gets the print calendar week property of this calendar.
     *
     * @return bool
     */
    public function getPrintCalendarWeek(): bool
    {
        return $this->print_calendar_week;
    }

    /**
     * Sets the print calendar week property of this calendar.
     *
     * @param bool $print_calendar_week
     * @return $this
     */
    public function setPrintCalendarWeek(bool $print_calendar_week): self
    {
        $this->print_calendar_week = $print_calendar_week;

        return $this;
    }

    /**
     * Gets the print week number property of this calendar.
     *
     * @return bool
     */
    public function getPrintWeekNumber(): bool
    {
        return $this->print_week_number;
    }

    /**
     * Sets the print week number property of this calendar.
     *
     * @param bool $print_week_number
     * @return $this
     */
    public function setPrintWeekNumber(bool $print_week_number): self
    {
        $this->print_week_number = $print_week_number;

        return $this;
    }

    /**
     * Gets the print qr code month property of this calendar.
     *
     * @return bool
     */
    public function getPrintQrCodeMonth(): bool
    {
        return $this->print_qr_code_month;
    }

    /**
     * Sets the print qr code month property of this calendar.
     *
     * @param bool $print_qr_code_month
     * @return $this
     */
    public function setPrintQrCodeMonth(bool $print_qr_code_month): self
    {
        $this->print_qr_code_month = $print_qr_code_month;

        return $this;
    }

    /**
     * Gets the print qr code title property of this calendar.
     *
     * @return bool
     */
    public function getPrintQrCodeTitle(): bool
    {
        return $this->print_qr_code_title;
    }

    /**
     * Sets the print qr code title property of this calendar.
     *
     * @param bool $print_qr_code_title
     * @return $this
     */
    public function setPrintQrCodeTitle(bool $print_qr_code_title): self
    {
        $this->print_qr_code_title = $print_qr_code_title;

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
