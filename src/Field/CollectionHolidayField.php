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

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * CollectionHolidayField class.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-09)
 * @package App\Field
 */
final class CollectionHolidayField implements FieldInterface
{
    use FieldTrait;

    /**
     * Create new CollectionCalendarImageField class.
     *
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/crud/field/collection_holiday.html.twig')
            ->setFormType(CollectionType::class)
            ->addCssClass('field-collection')
            ->addJsFiles(Asset::new('bundles/easyadmin/form-type-collection.js')->onlyOnForms())
            ->setDefaultColumns('col-md-8 col-xxl-7');
    }
}
