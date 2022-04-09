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
use App\Repository\HolidayGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class HolidayGroup
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: HolidayGroupRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['holiday_group_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended HolidayGroup resources.',
                'summary' => 'Retrieves the collection of extended HolidayGroup resources.',
            ],
            'path' => '/holiday_groups/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
        'get' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['holiday_group_extended']],
            'openapi_context' => [
                'description' => 'Retrieves a extended HolidayGroup resource.',
                'summary' => 'Retrieves a extended HolidayGroup resource.',
            ],
            'path' => '/holiday_groups/{id}/extended.{_format}',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
        'put' => [
            'normalization_context' => ['groups' => ['holiday_group']],
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['holiday_group']],
    order: ['id' => 'ASC'],
)]
class HolidayGroup implements EntityInterface
{
    use TimestampsTrait;

    public const CRUD_FIELDS_ADMIN = [];

    public const CRUD_FIELDS_REGISTERED = ['id', 'name', 'nameShort', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'name', 'nameShort', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'name', 'nameShort'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'name', 'nameShort', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_FILTER = ['name'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    private string $name;

    /** @var Collection<int, Holiday> $holidays */
    #[ORM\OneToMany(mappedBy: 'holidayGroup', targetEntity: Holiday::class)]
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    private Collection $holidays;

    #[ORM\Column(name: 'name_short', type: 'string', length: 10)]
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    private string $nameShort;

    /**
     * HolidayGroup constructor.
     */
    #[Pure]
    public function __construct()
    {
        $this->holidays = new ArrayCollection();
    }

    /**
     * __toString method.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Gets the id of this holiday group.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the name of this holiday group.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this holiday group.
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
     * Gets the short name of this holiday group.
     *
     * @return string
     */
    public function getNameShort(): string
    {
        return $this->nameShort;
    }

    /**
     * Sets the short name of this holiday group.
     *
     * @param string $nameShort
     * @return $this
     */
    public function setNameShort(string $nameShort): self
    {
        $this->nameShort = $nameShort;

        return $this;
    }

    /**
     * Gets all related holidays.
     *
     * @return Collection<int, Holiday>
     */
    public function getHolidays(): Collection
    {
        return $this->holidays;
    }

    /**
     * Gets all related holidays as simple id list.
     *
     * @return Collection<int, int>
     */
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    public function getHolidayIds(): Collection
    {
        return $this->getHolidays()->map(function (Holiday $holiday) {
            return $holiday->getId();
        });
    }

    /**
     * Gets all related holidays as array.
     *
     * @return array<string>
     */
    public function getHolidayArray(): array
    {
        $holidays = [];

        foreach ($this->holidays as $holiday) {
            $holidays[$holiday->getDate()->format('Y-m-d')] = $holiday->getName();
        }

        return $holidays;
    }

    /**
     * Adds a related holiday.
     *
     * @param Holiday $holiday
     * @return $this
     */
    public function addHoliday(Holiday $holiday): self
    {
        if (!$this->holidays->contains($holiday)) {
            $this->holidays[] = $holiday;
            $holiday->setHolidayGroup($this);
        }

        return $this;
    }

    /**
     * Removes given related holiday.
     *
     * @param Holiday $holiday
     * @return $this
     */
    public function removeHoliday(Holiday $holiday): self
    {
        if ($this->holidays->removeElement($holiday)) {
            // set the owning side to null (unless already changed)
            if ($holiday->getHolidayGroup() === $this) {
                $holiday->setHolidayGroup(null);
            }
        }

        return $this;
    }
}
