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

namespace App\Field\Configurator;

use App\Field\CollectionCalendarImageField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use JetBrains\PhpStorm\Pure;

/**
 * CollectionCalendarImageConfigurator
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-19)
 * @package App\Field\Configurator
 */
final class CollectionCalendarImageConfigurator implements FieldConfiguratorInterface
{
    /**
     * Set CollectionCalendarImageField as supported.
     *
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    #[Pure]
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return CollectionCalendarImageField::class === $field->getFieldFqcn();
    }

    /**
     * Configures this class.
     *
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @return void
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
    }
}
