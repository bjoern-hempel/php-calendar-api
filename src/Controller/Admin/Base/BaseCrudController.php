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

namespace App\Controller\Admin\Base;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\CalendarStyle;
use App\Entity\Event;
use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use App\Entity\Image;
use App\Entity\User;
use App\Utils\JsonConverter;
use App\Utils\SizeConverter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;

/**
 * Class BaseCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin\Base
 */
abstract class BaseCrudController extends AbstractCrudController
{
    abstract public function getEntity(): string;

    protected string $crudName;

    /**
     * Returns the entity of this class.
     *
     * @param string|null $entity
     * @param bool $doNotUseCache
     * @return string
     * @throws Exception
     */
    public function getCrudName(?string $entity = null, bool $doNotUseCache = false): string
    {
        if ($entity === null && isset($this->crudName) && !$doNotUseCache) {
            return $this->crudName;
        }

        $split = preg_split('~\\\\~', $entity ?? $this->getEntity());

        if ($split === false) {
            throw new Exception(sprintf('Unable to split string (%s:%d)', __FILE__, __LINE__));
        }

        $crudName = lcfirst($split[count($split) - 1]);

        if ($entity === null) {
            $this->crudName = $crudName;
        }

        return $crudName;
    }

    /**
     * Returns the field by given name.
     *
     * @param string $fieldName
     * @return FieldInterface
     * @throws Exception
     */
    protected function getField(string $fieldName): FieldInterface
    {
        /* Check if given field name is a registered name. */
        if (!in_array($fieldName, $this->getConstant('CRUD_FIELDS_REGISTERED'))) {
            throw new Exception(sprintf('Unknown FieldInterface "%s" (%s:%d).', $fieldName, __FILE__, __LINE__));
        }

        /* Special crud names. */
        switch ($this->getCrudName()) {

            case $this->getCrudName(Calendar::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'user':
                    case 'holidayGroup':
                    case 'calendarStyle':
                        return AssociationField::new($fieldName)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
                break;

            case $this->getCrudName(CalendarImage::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'user':
                    case 'calendar':
                    case 'image':
                        return AssociationField::new($fieldName)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));

                    case 'year':
                    case 'month':
                        return IntegerField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
                break;

            case $this->getCrudName(Event::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'user':
                        return AssociationField::new($fieldName)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));

                    /* Field type */
                    case 'type':
                        return IntegerField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
                break;

            case $this->getCrudName(Holiday::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'holidayGroup':
                        return AssociationField::new($fieldName)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
                break;

            case $this->getCrudName(Image::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'width':
                    case 'height':
                    return IntegerField::new($fieldName)
                        ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                        ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
                        ->formatValue(function ($value) {
                            return sprintf('%d px', $value);
                        });
                    case 'size':
                        return IntegerField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
                            ->formatValue(function ($value) {
                                return SizeConverter::getHumanReadableSize($value);
                            });
                }
                break;

            case $this->getCrudName(User::class):
                switch ($fieldName) {

                    /* Field roles */
                    case 'roles':
                        return ArrayField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
        }

        /* All other crud names (default fields for all other entities) */
        return match ($fieldName) {

            /* Field id */
            'id' => IdField::new($fieldName)
                ->hideOnForm()
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),

            /* Field configJson */
            'configJson' => CodeEditorField::new($fieldName)
                /* Not called within formulas. */
                ->formatValue(
                    function ($json) {
                        return (new JsonConverter($json))->getBeautified(2);
                    }
                )
                ->setLanguage('css')
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),

            /* DateTime fields. */
            'date' => DateField::new($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),

            /* DateTime fields. */
            'updatedAt', 'createdAt' => DateTimeField::new($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),

            /* All other fields. */
            default => TextField::new($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
        };
    }

    /**
     * Returns the constant from fqcn entity.
     *
     * @param string $name
     * @return string[]
     * @throws Exception
     */
    public function getConstant(string $name): array
    {
        $constant = constant(sprintf('%s::%s', $this->getEntity(), $name));

        if (!is_array($constant)) {
            throw new Exception(sprintf('Unexpected constant returned (%s:%d).', __FILE__, __LINE__));
        }

        /* Check given constant */
        if (!in_array(serialize($constant), array_unique([
            serialize(Calendar::CRUD_FIELDS_REGISTERED),
            serialize(Calendar::CRUD_FIELDS_INDEX),
            serialize(Calendar::CRUD_FIELDS_NEW),
            serialize(Calendar::CRUD_FIELDS_EDIT),
            serialize(Calendar::CRUD_FIELDS_DETAIL),

            serialize(CalendarImage::CRUD_FIELDS_REGISTERED),
            serialize(CalendarImage::CRUD_FIELDS_INDEX),
            serialize(CalendarImage::CRUD_FIELDS_NEW),
            serialize(CalendarImage::CRUD_FIELDS_EDIT),
            serialize(CalendarImage::CRUD_FIELDS_DETAIL),

            serialize(CalendarStyle::CRUD_FIELDS_REGISTERED),
            serialize(CalendarStyle::CRUD_FIELDS_INDEX),
            serialize(CalendarStyle::CRUD_FIELDS_NEW),
            serialize(CalendarStyle::CRUD_FIELDS_EDIT),
            serialize(CalendarStyle::CRUD_FIELDS_DETAIL),

            serialize(Event::CRUD_FIELDS_REGISTERED),
            serialize(Event::CRUD_FIELDS_INDEX),
            serialize(Event::CRUD_FIELDS_NEW),
            serialize(Event::CRUD_FIELDS_EDIT),
            serialize(Event::CRUD_FIELDS_DETAIL),

            serialize(Holiday::CRUD_FIELDS_REGISTERED),
            serialize(Holiday::CRUD_FIELDS_INDEX),
            serialize(Holiday::CRUD_FIELDS_NEW),
            serialize(Holiday::CRUD_FIELDS_EDIT),
            serialize(Holiday::CRUD_FIELDS_DETAIL),

            serialize(HolidayGroup::CRUD_FIELDS_REGISTERED),
            serialize(HolidayGroup::CRUD_FIELDS_INDEX),
            serialize(HolidayGroup::CRUD_FIELDS_NEW),
            serialize(HolidayGroup::CRUD_FIELDS_EDIT),
            serialize(HolidayGroup::CRUD_FIELDS_DETAIL),

            serialize(Image::CRUD_FIELDS_REGISTERED),
            serialize(Image::CRUD_FIELDS_INDEX),
            serialize(Image::CRUD_FIELDS_NEW),
            serialize(Image::CRUD_FIELDS_EDIT),
            serialize(Image::CRUD_FIELDS_DETAIL),

            serialize(User::CRUD_FIELDS_REGISTERED),
            serialize(User::CRUD_FIELDS_INDEX),
            serialize(User::CRUD_FIELDS_NEW),
            serialize(User::CRUD_FIELDS_EDIT),
            serialize(User::CRUD_FIELDS_DETAIL),
        ]))) {
            throw new Exception(sprintf('Unsupported constant (%s:%d).', __FILE__, __LINE__));
        }

        return $constant;
    }

    /**
     * Configure actions.
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    /**
     * Configures the fields to be displayed (index page)
     *
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFieldsIndex(): iterable
    {
        foreach ($this->getConstant('CRUD_FIELDS_INDEX') as $fieldName) {
            yield $this->getField($fieldName);
        }
    }

    /**
     * Configures the fields to be displayed (new page)
     *
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFieldsNew(): iterable
    {
        foreach ($this->getConstant('CRUD_FIELDS_NEW') as $fieldName) {
            yield $this->getField($fieldName);
        }
    }

    /**
     * Configures the fields to be displayed (edit page)
     *
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFieldsEdit(): iterable
    {
        foreach ($this->getConstant('CRUD_FIELDS_EDIT') as $fieldName) {
            yield $this->getField($fieldName);
        }
    }

    /**
     * Configures the fields to be displayed (detail page)
     *
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFieldsDetail(): iterable
    {
        foreach ($this->getConstant('CRUD_FIELDS_DETAIL') as $fieldName) {
            yield $this->getField($fieldName);
        }
    }

    /**
     * Configures the fields to be displayed.
     *
     * @param string $pageName
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFields(string $pageName): iterable
    {
        return match ($pageName) {
            Crud::PAGE_NEW => $this->configureFieldsNew(),
            Crud::PAGE_EDIT => $this->configureFieldsEdit(),
            Crud::PAGE_DETAIL => $this->configureFieldsDetail(),
            default => $this->configureFieldsIndex(),
        };
    }

    /**
     * Configures crud.
     *
     * @param Crud $crud
     * @return Crud
     * @throws Exception
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(sprintf('admin.%s.singular', $this->getCrudName()))
            ->setEntityLabelInPlural(sprintf('admin.%s.plural', $this->getCrudName()));
    }
}
