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

namespace App\Entity\Trait;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait TimestampsTrait
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
#[ORM\HasLifecycleCallbacks]
trait TimestampsTrait
{
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * Gets the created at field of this trait.
     *
     * @return ?DateTimeImmutable
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the created at field of this trait.
     *
     * @param DateTimeImmutable $timestamp
     * @return User|Event|TimestampsTrait
     */
    public function setCreatedAt(DateTimeImmutable $timestamp): self
    {
        $this->createdAt = $timestamp;
        return $this;
    }

    /**
     * Gets the updated at field of this trait.
     *
     * @return ?DateTimeImmutable
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the updated at field of this trait.
     *
     * @param DateTimeImmutable $timestamp
     * @return User|Event|TimestampsTrait
     */
    public function setUpdatedAt(DateTimeImmutable $timestamp): self
    {
        $this->updatedAt = $timestamp;
        return $this;
    }

    /**
     * Sets automatically the created at field.
     */
    #[ORM\PrePersist]
    public function setCreatedAtAutomatically()
    {
        if ($this->createdAt === null) {
            $this->setCreatedAt(new DateTimeImmutable());
        }
    }

    /**
     * Sets automatically the updated at field.
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtAutomatically()
    {
        $this->setUpdatedAt(new DateTimeImmutable());
    }
}
