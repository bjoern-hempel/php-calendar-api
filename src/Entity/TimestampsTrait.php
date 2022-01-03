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
