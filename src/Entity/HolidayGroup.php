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

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Trait\TimestampsTrait;
use App\EventListener\Entity\UserListener;
use App\Repository\HolidayGroupRepository;
use App\Utils\HolidayCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class HolidayGroup
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-21)
 * @since 0.1.1 (2022-11-21) Update to symfony 6.1
 * @since 0.1.0 (2021-12-30) First version.
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: HolidayGroupRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['holiday_group']]
        ),
        new GetCollection(
            uriTemplate: '/holiday_groups/extended.{_format}',
            openapiContext: [
                'description' => 'Retrieves the collection of extended HolidayGroup resources.',
                'summary' => 'Retrieves the collection of extended HolidayGroup resources.',
            ],
            normalizationContext: ['groups' => ['holiday_group_extended']]
        ),
        new Post(
            normalizationContext: ['groups' => ['holiday_group']]
        ),

        new Delete(
            normalizationContext: ['groups' => ['holiday_group']]
        ),
        new Get(
            normalizationContext: ['groups' => ['holiday_group']]
        ),
        new Get(
            uriTemplate: '/holiday_groups/{id}/extended.{_format}',
            openapiContext: [
                'description' => 'Retrieves a extended HolidayGroup resource.',
                'summary' => 'Retrieves a extended HolidayGroup resource.',
            ],
            normalizationContext: ['groups' => ['holiday_group_extended']]
        ),
        new Patch(
            normalizationContext: ['groups' => ['holiday_group']]
        ),
        new Put(
            normalizationContext: ['groups' => ['holiday_group']]
        )
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['holiday_group']],
    order: ['id' => 'ASC'],
)]
class HolidayGroup implements EntityInterface, \Stringable
{
    use TimestampsTrait;

    final public const CRUD_FIELDS_ADMIN = [];

    final public const CRUD_FIELDS_REGISTERED = ['id', 'name', 'nameShort', 'holidays', 'holidaysGrouped', 'updatedAt', 'createdAt'];

    final public const CRUD_FIELDS_INDEX = ['id', 'name', 'nameShort', 'holidaysGrouped', 'updatedAt', 'createdAt'];

    final public const CRUD_FIELDS_NEW = ['id', 'name', 'nameShort'];

    final public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    final public const CRUD_FIELDS_DETAIL = ['id', 'name', 'nameShort', 'holidaysGrouped', 'updatedAt', 'createdAt'];

    final public const CRUD_FIELDS_FILTER = ['name'];

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
    #[ORM\OrderBy(value: ['date' => 'ASC'])]
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
     * Gets all related holidays grouped.
     *
     * @return Array<string, Array<int, Collection<int, Holiday>>|HolidayGroup>
     * @throws Exception
     */
    public function getHolidaysGrouped(): array
    {
        return [
            'holidayGroup' => $this,
            'holidays' => HolidayCollection::getHolidaysGrouped($this->getHolidays())
        ];
    }

    /**
     * Gets all related holidays as simple id list.
     *
     * @return Collection<int, int>
     */
    #[Groups(['holiday_group', 'holiday_group_extended'])]
    public function getHolidayIds(): Collection
    {
        return $this->getHolidays()->map(fn(Holiday $holiday) => $holiday->getId());
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
