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
use App\Repository\PlaceRepository;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class Place
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-05-08)
 * @package App\Entity
 *
 * @see http://download.geonames.org/export/dump/readme.txt (The main 'geoname' table has the following fields.)
 */
#[ORM\Entity(repositoryClass: PlaceRepository::class)]
#[ApiResource]
class Place
{
    use TimestampsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'geoname_id', type: 'integer')]
    private int $geonameId;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $name;

    #[ORM\Column(name: 'ascii_name', type: 'string', length: 1024)]
    private string $asciiName;

    #[ORM\Column(name: 'alternate_names', type: 'string', length: 4096)]
    private string $alternateNames;

    #[ORM\Column(type: 'point')]
    private Point $coordinate;

    #[ORM\Column(name: 'feature_class', type: 'string', length: 1)]
    private string $featureClass;

    #[ORM\Column(name: 'feature_code', type: 'string', length: 10)]
    private string $featureCode;

    #[ORM\Column(name: 'country_code', type: 'string', length: 2)]
    private string $countryCode;

    #[ORM\Column(type: 'string', length: 200)]
    private string $cc2;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?string $population = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $elevation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dem = null;

    #[ORM\Column(type: 'string', length: 40)]
    private string $timezone;

    #[ORM\Column(name: 'modification_date', type: 'date')]
    private DateTime $modificationDate;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $admin1Code = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $admin2Code = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $admin3Code = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $admin4Code = null;

    private float $distance = .0;

    /**
     * Sets the id of this place.
     *
     * @param int $id
     * @return Place
     */
    public function setIdTmp(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the id of this place.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the geo name id of this place.
     *
     * @return int
     */
    public function getGeonameId(): int
    {
        return $this->geonameId;
    }

    /**
     * Sets the geo name id of this place.
     *
     * @param int $geonameId
     * @return $this
     */
    public function setGeonameId(int $geonameId): self
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    /**
     * Gets the name of this place.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this place.
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
     * Gets the ascii name of this place.
     *
     * @return string
     */
    public function getAsciiName(): string
    {
        return $this->asciiName;
    }

    /**
     * Sets the ascii name of this place.
     *
     * @param string $asciiName
     * @return $this
     */
    public function setAsciiName(string $asciiName): self
    {
        $this->asciiName = $asciiName;

        return $this;
    }

    /**
     * Gets the alternate name of this place.
     *
     * @return string
     */
    public function getAlternateNames(): string
    {
        return $this->alternateNames;
    }

    /**
     * Sets the alternate name of this place.
     *
     * @param string $alternateNames
     * @return $this
     */
    public function setAlternateNames(string $alternateNames): self
    {
        $this->alternateNames = $alternateNames;

        return $this;
    }

    /**
     * Gets the point of this place.
     *
     * @return Point
     */
    public function getCoordinate(): Point
    {
        return $this->coordinate;
    }

    /**
     * Sets the point of this place.
     *
     * @param Point $coordinate
     * @return $this
     */
    public function setCoordinate(Point $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    /**
     * Gets the feature class of this place.
     *
     * @return string
     */
    public function getFeatureClass(): string
    {
        return $this->featureClass;
    }

    /**
     * Sets the feature class of this place.
     *
     * @param string $featureClass
     * @return $this
     */
    public function setFeatureClass(string $featureClass): self
    {
        $this->featureClass = $featureClass;

        return $this;
    }

    /**
     * Gets the feature code of this place.
     *
     * @return string
     */
    public function getFeatureCode(): string
    {
        return $this->featureCode;
    }

    /**
     * Sets the feature code of this place.
     *
     * @param string $featureCode
     * @return $this
     */
    public function setFeatureCode(string $featureCode): self
    {
        $this->featureCode = $featureCode;

        return $this;
    }

    /**
     * Gets the country code of this place.
     *
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Sets the country code of this place.
     *
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Gets the cc2 of this place.
     *
     * @return string
     */
    public function getCc2(): string
    {
        return $this->cc2;
    }

    /**
     * Sets the cc2 of this place.
     *
     * @param string $cc2
     * @return $this
     */
    public function setCc2(string $cc2): self
    {
        $this->cc2 = $cc2;

        return $this;
    }

    /**
     * Gets the population of this place.
     *
     * @return string|null
     */
    public function getPopulation(): ?string
    {
        return $this->population;
    }

    /**
     * Sets the population of this place.
     *
     * @param string|null $population
     * @return $this
     */
    public function setPopulation(?string $population): self
    {
        $this->population = $population;

        return $this;
    }

    /**
     * Gets the elevation of this place.
     *
     * @return int|null
     */
    public function getElevation(): ?int
    {
        return $this->elevation;
    }

    /**
     * Sets the elevation of this place.
     *
     * @param int|null $elevation
     * @return $this
     */
    public function setElevation(?int $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Gets the dem of this place.
     *
     * @return int|null
     */
    public function getDem(): ?int
    {
        return $this->dem;
    }

    /**
     * Sets the dem of this place.
     *
     * @param int|null $dem
     * @return $this
     */
    public function setDem(?int $dem): self
    {
        $this->dem = $dem;

        return $this;
    }

    /**
     * Gets the timezone of this place.
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Sets the timezone of this place.
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Gets the modification date of this place.
     *
     * @return DateTime
     */
    public function getModificationDate(): DateTime
    {
        return $this->modificationDate;
    }

    /**
     * Sets the modification date of this place.
     *
     * @param DateTime $modificationDate
     * @return $this
     */
    public function setModificationDate(DateTime $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * Gets admin1 code of this place.
     *
     * @return string|null
     */
    public function getAdmin1Code(): ?string
    {
        return $this->admin1Code;
    }

    /**
     * Sets admin1 code of this place.
     *
     * @param string|null $admin1Code
     * @return $this
     */
    public function setAdmin1Code(?string $admin1Code): self
    {
        $this->admin1Code = $admin1Code;

        return $this;
    }

    /**
     * Gets admin2 code of this place.
     *
     * @return string|null
     */
    public function getAdmin2Code(): ?string
    {
        return $this->admin2Code;
    }

    /**
     * Sets admin2 code of this place.
     *
     * @param string|null $admin2Code
     * @return $this
     */
    public function setAdmin2Code(?string $admin2Code): self
    {
        $this->admin2Code = $admin2Code;

        return $this;
    }

    /**
     * Gets admin3 code of this place.
     *
     * @return string|null
     */
    public function getAdmin3Code(): ?string
    {
        return $this->admin3Code;
    }

    /**
     * Sets admin3 code of this place.
     *
     * @param string|null $admin3Code
     * @return $this
     */
    public function setAdmin3Code(?string $admin3Code): self
    {
        $this->admin3Code = $admin3Code;

        return $this;
    }

    /**
     * Gets admin4 code of this place.
     *
     * @return string|null
     */
    public function getAdmin4Code(): ?string
    {
        return $this->admin4Code;
    }

    /**
     * Sets admin4 code of this place.
     *
     * @param string|null $admin4Code
     * @return $this
     */
    public function setAdmin4Code(?string $admin4Code): self
    {
        $this->admin4Code = $admin4Code;

        return $this;
    }

    /**
     * Gets distance of this place (if given from select query to a given place). Not used for db.
     *
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * Sets distance of this place (if given from select query to a given place). Not used for db.
     *
     * @param float $distance
     * @return Place
     */
    public function setDistance(float $distance): Place
    {
        $this->distance = $distance;
        return $this;
    }
}
