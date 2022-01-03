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

use App\Repository\HolidayRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class Holiday
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Holiday
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: HolidayGroup::class, inversedBy: 'holidays')]
    private ?HolidayGroup $holiday_group;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $date;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $color;

    /**
     * Gets the id of this holiday.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the holiday group of this holiday.
     *
     * @return HolidayGroup|null
     */
    public function getHolidayGroup(): ?HolidayGroup
    {
        return $this->holiday_group;
    }

    /**
     * Sets the holiday group of this holiday.
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
     * Gets the name of this holiday.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this holiday.
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
     * Gets the date of this holiday.
     *
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Sets the date of this holiday.
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
     * Gets the color of this holiday.
     *
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Sets the color of this holiday.
     *
     * @param string|null $color
     * @return $this
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
