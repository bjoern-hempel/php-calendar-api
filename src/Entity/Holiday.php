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
use App\Repository\HolidayRepository;
use App\Utils\ArrayToObject;
use App\Utils\Traits\JsonHelper;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class Holiday
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: HolidayRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['holiday_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended Holiday resources.',
                'summary' => 'Retrieves the collection of extended Holiday resources.',
            ],
            'path' => '/holidays/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
        'get' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['holiday_extended']],
            'openapi_context' => [
                'description' => 'Retrieves a extended Holiday resource.',
                'summary' => 'Retrieves a extended Holiday resource.',
            ],
            'path' => '/holidays/{id}/extended.{_format}',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
        'put' => [
            'normalization_context' => ['groups' => ['holiday']],
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['holiday']],
    order: ['id' => 'ASC'],
)]
class Holiday
{
    use TimestampsTrait;

    use JsonHelper;

    public const CRUD_FIELDS_REGISTERED = ['id', 'holidayGroup', 'name', 'date', 'configJson', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_INDEX = ['id', 'holidayGroup', 'name', 'date', 'configJson', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_NEW = ['id', 'holidayGroup', 'name', 'date', 'configJson', 'updatedAt', 'createdAt'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'holidayGroup', 'name', 'date', 'configJson', 'updatedAt', 'createdAt'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['holiday', 'holiday_extended'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: HolidayGroup::class, inversedBy: 'holidays')]
    #[Groups(['holiday', 'holiday_extended'])]
    private ?HolidayGroup $holidayGroup;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['holiday', 'holiday_extended'])]
    private string $name;

    #[ORM\Column(type: 'date')]
    #[Groups(['holiday', 'holiday_extended'])]
    private DateTimeInterface $date;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups(['holiday', 'holiday_extended'])]
    private array $config = [];

    private ArrayToObject $configObject;

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
        return $this->holidayGroup;
    }

    /**
     * Gets the holiday group id of this holiday.
     *
     * @return int|null
     */
    #[Groups(['holiday', 'holiday_extended'])]
    public function getHolidayGroupId(): ?int
    {
        return $this->getHolidayGroup()?->getId();
    }

    /**
     * Sets the holiday group of this holiday.
     *
     * @param HolidayGroup|null $holidayGroup
     * @return $this
     */
    public function setHolidayGroup(?HolidayGroup $holidayGroup): self
    {
        $this->holidayGroup = $holidayGroup;

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
