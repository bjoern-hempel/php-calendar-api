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
use App\Repository\CalendarStyleRepository;
use App\Utils\Traits\JsonHelper;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entity class CalendarStyle
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: CalendarStyleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_style_extended']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of extended CalendarStyle resources.',
                'summary' => 'Retrieves the collection of extended CalendarStyle resources.',
            ],
            'path' => '/calendar_styles/extended.{_format}',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
    ],
    itemOperations: [
        'delete' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
        'get' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
        'get_extended' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['calendar_style_extended']],
            'openapi_context' => [
                'description' => 'Retrieves a extended CalendarStyle resource.',
                'summary' => 'Retrieves a extended CalendarStyle resource.',
            ],
            'path' => '/calendar_styles/{id}/extended.{_format}',
        ],
        'patch' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
        'put' => [
            'normalization_context' => ['groups' => ['calendar_style']],
        ],
    ],
    normalizationContext: ['enable_max_depth' => true, 'groups' => ['calendar_style']],
    order: ['id' => 'ASC'],
)]
class CalendarStyle
{
    use TimestampsTrait;

    use JsonHelper;

    public const CRUD_FIELDS_REGISTERED = ['id', 'name', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_INDEX = ['id', 'name', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_NEW = ['id', 'name', 'updatedAt', 'createdAt', 'configJson'];

    public const CRUD_FIELDS_EDIT = self::CRUD_FIELDS_NEW;

    public const CRUD_FIELDS_DETAIL = ['id', 'name', 'updatedAt', 'createdAt', 'configJson'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['calendar_style', 'calendar_style_extended'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['calendar_style', 'calendar_style_extended'])]
    private string $name;

    /** @var array<string|int|float|bool> $config */
    #[ORM\Column(type: 'json')]
    #[Groups(['calendar_style', 'calendar_style_extended'])]
    private array $config = [];

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
     * Gets the id of this calendar style.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the name of this calendar style.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the id of this calendar style.
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
     * Gets the config.
     *
     * @return array<string|int|float|bool>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Sets the config.
     *
     * @param array<string|int|float|bool> $config
     * @return $this
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

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
