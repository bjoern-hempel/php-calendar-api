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
use App\Entity\CalendarStyle;
use App\Entity\Event;
use App\Entity\Holiday;
use App\Entity\User;
use App\Utils\JsonConverter;
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

    /**
     * Returns the entity of this class.
     *
     * @return string
     * @throws Exception
     */
    public function getCrudName(): string
    {
        $split = preg_split('~\\\\~', $this->getEntity());

        if ($split === false) {
            throw new Exception(sprintf('Unable to split string (%s:%d)', __FILE__, __LINE__));
        }

        return lcfirst($split[count($split) - 1]);
    }

    /**
     * Returns the field by given name.
     *
     * @param string $name
     * @return FieldInterface
     * @throws Exception
     */
    protected function getField(string $name): FieldInterface
    {
        /* Check if given field name is a registered name. */
        if (!in_array($name, $this->getConstant('CRUD_FIELDS_REGISTERED'))) {
            throw new Exception(sprintf('Unknown FieldInterface "%s" (%s:%d).', $name, __FILE__, __LINE__));
        }

        /* Special crud names. */
        switch ($this->getCrudName()) {
            case 'calendar':
                switch ($name) {

                    /* Association field. */
                    case 'user':
                    case 'holidayGroup':
                    case 'calendarStyle':
                        return AssociationField::new($name)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name));
                }
                break;

            case 'event':
                switch ($name) {

                    /* Association field. */
                    case 'user':
                        return AssociationField::new($name)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name));

                    /* Field type */
                    case 'type':
                        return IntegerField::new($name)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name));
                }
                break;

            case 'holiday':
                switch ($name) {

                    /* Association field. */
                    case 'holidayGroup':
                        return AssociationField::new($name)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name));
                }
                break;

            case 'user':
                switch ($name) {

                    /* Field roles */
                    case 'roles':
                        return ArrayField::new($name)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name));
                }
        }

        /* All other crud names (default fields for all other entities) */
        return match ($name) {

            /* Field id */
            'id' => IdField::new($name)
                ->hideOnForm()
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name)),

            /* Field configJson */
            'configJson' => CodeEditorField::new($name)
                /* Not called within formulas. */
                ->formatValue(
                    function ($json) {
                        return (new JsonConverter($json))->getBeautified(2);
                    }
                )
                ->setLanguage('css')
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name)),

            /* DateTime fields. */
            'date' => DateField::new($name)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name)),

            /* DateTime fields. */
            'updatedAt', 'createdAt' => DateTimeField::new($name)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name)),

            /* All other fields. */
            default => TextField::new($name)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $name))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $name)),
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
            serialize(CalendarStyle::CRUD_FIELDS_REGISTERED),
            serialize(CalendarStyle::CRUD_FIELDS_INDEX),
            serialize(CalendarStyle::CRUD_FIELDS_NEW),
            serialize(CalendarStyle::CRUD_FIELDS_EDIT),
            serialize(CalendarStyle::CRUD_FIELDS_DETAIL),

            serialize(Calendar::CRUD_FIELDS_REGISTERED),
            serialize(Calendar::CRUD_FIELDS_INDEX),
            serialize(Calendar::CRUD_FIELDS_NEW),
            serialize(Calendar::CRUD_FIELDS_EDIT),
            serialize(Calendar::CRUD_FIELDS_DETAIL),

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
}
