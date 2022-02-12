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

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Utils\JsonConverter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;

/**
 * Class EventCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin
 */
class EventCrudController extends AbstractCrudController
{
    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Event::class;
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
     * Returns the field by given name.
     *
     * @param string $name
     * @return FieldInterface
     * @throws Exception
     */
    protected function getField(string $name): FieldInterface
    {
        /* Check if given field name is a registered name */
        if (!in_array($name, Event::CRUD_FIELDS_REGISTERED)) {
            throw new Exception(sprintf('Unknown FieldInterface "%s" (%s:%d).', $name, __FILE__, __LINE__));
        }

        return match ($name) {

            /* Field id */
            'id' => IdField::new($name)
                ->hideOnForm()
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),

            /* Field id */
            'user' => AssociationField::new($name)
                ->setRequired(true)
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),

            /* Field id */
            'type' => IntegerField::new($name)
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),

            /* DateTime fields. */
            'date' => DateField::new($name)
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),

            /* Field configJson */
            'configJson' => CodeEditorField::new($name)
                /* Not called within formulas. */
                ->formatValue(
                    function ($json) {
                        return (new JsonConverter($json))->getBeautified(2);
                    }
                )
                ->setLanguage('css')
                ->setLabel('admin.event.fields.config.label')
                ->setHelp('admin.event.fields.config.help'),

            /* DateTime fields. */
            'updatedAt', 'createdAt' => DateTimeField::new($name)
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),

            /* All other fields. */
            default => TextField::new($name)
                ->setLabel(sprintf('admin.event.fields.%s.label', $name))
                ->setHelp(sprintf('admin.event.fields.%s.help', $name)),
        };
    }

    /**
     * Configures the fields to be displayed (index page)
     *
     * @return FieldInterface[]
     * @throws Exception
     */
    public function configureFieldsIndex(): iterable
    {
        foreach (Event::CRUD_FIELDS_INDEX as $fieldName) {
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
        foreach (Event::CRUD_FIELDS_NEW as $fieldName) {
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
        foreach (Event::CRUD_FIELDS_EDIT as $fieldName) {
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
        foreach (Event::CRUD_FIELDS_DETAIL as $fieldName) {
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
