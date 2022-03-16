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
use App\Field\PathImageField;
use App\Service\SecurityService;
use App\Utils\EasyAdminField;
use App\Utils\JsonConverter;
use App\Utils\SizeConverter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    protected SecurityService $securityService;

    protected TranslatorInterface $translator;

    protected EasyAdminField $easyAdminField;

    protected const CRUD_FIELDS_ADMIN = 'CRUD_FIELDS_ADMIN';

    /**
     * BaseCrudController constructor.
     *
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator)
    {
        $this->securityService = $securityService;

        $this->translator = $translator;

        $this->easyAdminField = new EasyAdminField($this->getCrudName());
    }

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
     * Returns the entity instance if possible.
     *
     * @return object|null
     */
    protected function getEntityInstance(): ?object
    {
        return $this->getContext()?->getEntity()->getInstance();
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

            case $this->getCrudName(Event::class):
                switch ($fieldName) {

                    /* Association field. */
                    case 'user':
                        return AssociationField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
                            ->setRequired(true);

                    /* Field type */
                    case 'type':
                        return ChoiceField::new($fieldName)
                            ->setChoices([
                                sprintf('admin.event.fields.type.entries.entry%d', 0) => 0,
                                sprintf('admin.event.fields.type.entries.entry%d', 1) => 1,
                                sprintf('admin.event.fields.type.entries.entry%d', 2) => 2,
                            ])
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
                    case 'user':
                        return AssociationField::new($fieldName)
                            ->setRequired(true)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));

                    /* Property fields */
                    case 'name':
                        return TextField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));

                    /* Dimension fields. */
                    case 'width':
                    case 'height':
                    return IntegerField::new($fieldName)
                        ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                        ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
                        ->formatValue(function ($value) {
                            return sprintf('%d px', $value);
                        });

                    /* Size field */
                    case 'size':
                        return IntegerField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
                            ->formatValue(function ($value) {
                                return SizeConverter::getHumanReadableSize($value);
                            });

                    /* Full path fields */
                    case 'pathSourceFull':
                    case 'pathTargetFull':
                        return PathImageField::new($fieldName)
                            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
                }
                break;

            case $this->getCrudName(User::class):
                switch ($fieldName) {

                    /* Email field */
                    case 'email':
                        return $this->easyAdminField->getEmailField($fieldName);

                    /* Password field */
                    case 'plainPassword':
                    case 'password':
                        return $this->easyAdminField->getTextField($fieldName)
                            ->setFormType(PasswordType::class);

                    /* Field roles */
                    case 'roles':
                        return $this->easyAdminField->getChoiceField($fieldName, [
                            'roleUser' => User::ROLE_USER,
                            'roleAdmin' => User::ROLE_ADMIN,
                            'roleSuperAdmin' => User::ROLE_SUPER_ADMIN,
                        ])->allowMultipleChoices(true)->renderExpanded();
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

            /* Collection fields */
            'calendarImages' => CollectionField::new($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),

            /* All other fields. */
            default => $this->easyAdminField->getTextField($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
        };
    }

    /**
     * Returns the constant from fqcn entity (raw).
     *
     * @param string $name
     * @param string[]|null $default
     * @return string[]
     * @throws Exception
     */
    protected function getConstantRaw(string $name, ?array $default = null): array
    {
        $constantName = sprintf('%s::%s', $this->getEntity(), $name);

        if (!defined($constantName) && $default !== null) {
            return $default;
        }

        if (!defined($constantName)) {
            throw new Exception(sprintf('The constant "%s" does not exist (%s:%d).', $constantName, __FILE__, __LINE__));
        }

        $value = constant($constantName);

        if (!is_array($value)) {
            throw new Exception(sprintf('Unexpected constant returned (%s:%d).', __FILE__, __LINE__));
        }

        return $value;
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
        $constant = $this->getConstantRaw($name);

        /* Check given constant */
        if (!in_array(serialize($constant), array_unique([
            serialize(Calendar::CRUD_FIELDS_ADMIN),
            serialize(Calendar::CRUD_FIELDS_REGISTERED),
            serialize(Calendar::CRUD_FIELDS_INDEX),
            serialize(Calendar::CRUD_FIELDS_NEW),
            serialize(Calendar::CRUD_FIELDS_EDIT),
            serialize(Calendar::CRUD_FIELDS_DETAIL),
            serialize(Calendar::CRUD_FIELDS_FILTER),

            serialize(CalendarImage::CRUD_FIELDS_ADMIN),
            serialize(CalendarImage::CRUD_FIELDS_REGISTERED),
            serialize(CalendarImage::CRUD_FIELDS_INDEX),
            serialize(CalendarImage::CRUD_FIELDS_NEW),
            serialize(CalendarImage::CRUD_FIELDS_EDIT),
            serialize(CalendarImage::CRUD_FIELDS_DETAIL),
            serialize(CalendarImage::CRUD_FIELDS_FILTER),

            serialize(CalendarStyle::CRUD_FIELDS_ADMIN),
            serialize(CalendarStyle::CRUD_FIELDS_REGISTERED),
            serialize(CalendarStyle::CRUD_FIELDS_INDEX),
            serialize(CalendarStyle::CRUD_FIELDS_NEW),
            serialize(CalendarStyle::CRUD_FIELDS_EDIT),
            serialize(CalendarStyle::CRUD_FIELDS_DETAIL),
            serialize(CalendarStyle::CRUD_FIELDS_FILTER),

            serialize(Event::CRUD_FIELDS_ADMIN),
            serialize(Event::CRUD_FIELDS_REGISTERED),
            serialize(Event::CRUD_FIELDS_INDEX),
            serialize(Event::CRUD_FIELDS_NEW),
            serialize(Event::CRUD_FIELDS_EDIT),
            serialize(Event::CRUD_FIELDS_DETAIL),
            serialize(Event::CRUD_FIELDS_FILTER),

            serialize(Holiday::CRUD_FIELDS_ADMIN),
            serialize(Holiday::CRUD_FIELDS_REGISTERED),
            serialize(Holiday::CRUD_FIELDS_INDEX),
            serialize(Holiday::CRUD_FIELDS_NEW),
            serialize(Holiday::CRUD_FIELDS_EDIT),
            serialize(Holiday::CRUD_FIELDS_DETAIL),
            serialize(Holiday::CRUD_FIELDS_FILTER),

            serialize(HolidayGroup::CRUD_FIELDS_ADMIN),
            serialize(HolidayGroup::CRUD_FIELDS_REGISTERED),
            serialize(HolidayGroup::CRUD_FIELDS_INDEX),
            serialize(HolidayGroup::CRUD_FIELDS_NEW),
            serialize(HolidayGroup::CRUD_FIELDS_EDIT),
            serialize(HolidayGroup::CRUD_FIELDS_DETAIL),
            serialize(HolidayGroup::CRUD_FIELDS_FILTER),

            serialize(Image::CRUD_FIELDS_ADMIN),
            serialize(Image::CRUD_FIELDS_REGISTERED),
            serialize(Image::CRUD_FIELDS_INDEX),
            serialize(Image::CRUD_FIELDS_NEW),
            serialize(Image::CRUD_FIELDS_EDIT),
            serialize(Image::CRUD_FIELDS_DETAIL),
            serialize(Image::CRUD_FIELDS_FILTER),

            serialize(User::CRUD_FIELDS_ADMIN),
            serialize(User::CRUD_FIELDS_REGISTERED),
            serialize(User::CRUD_FIELDS_INDEX),
            serialize(User::CRUD_FIELDS_NEW),
            serialize(User::CRUD_FIELDS_EDIT),
            serialize(User::CRUD_FIELDS_DETAIL),
            serialize(User::CRUD_FIELDS_FILTER),
        ]))) {
            throw new Exception(sprintf('Unsupported constant (%s:%d).', __FILE__, __LINE__));
        }

        if ($this->securityService->isGrantedByAnAdmin()) {
            return $constant;
        }

        return array_diff($constant, $this->getConstantRaw(self::CRUD_FIELDS_ADMIN));
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
            ->setEntityLabelInPlural(sprintf('admin.%s.plural', $this->getCrudName()))
            ->overrideTemplate('crud/detail', 'admin/crud/detail.html.twig')
            ->overrideTemplate('crud/index', 'admin/crud/index.html.twig');
    }

    /**
     * Adds user filter.
     *
     * @param QueryBuilder $qb
     * @param bool $own
     * @return QueryBuilder
     */
    protected function addUserFilter(QueryBuilder $qb, bool $own = false): QueryBuilder
    {
        /* These roles are allowed to see all entities. */
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $qb;
        }

        /* Filter by user */
        $qb->andWhere($own ? 'entity.id = :user' : 'entity.user = :user');
        $qb->setParameter('user', $this->getUser());

        return $qb;
    }

    /**
     * Check permissions.
     *
     * @param QueryBuilder $qb
     * @param string $entityName
     * @return QueryBuilder
     * @throws Exception
     */
    protected function checkPermissions(QueryBuilder $qb, string $entityName): QueryBuilder
    {
        /* These roles are allowed to see all entities. */
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $qb;
        }

        /* Every list will be empty -> If a non-permitted class is called anyway */
        throw new Exception(sprintf('You do not have permission to call the "%s" entity (%s:%d).', $entityName, __FILE__, __LINE__));
    }

    /**
     * Filters list by roles.
     *
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     * @throws Exception
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return match ($this->getEntity()) {
            /* Filter classes by user */
            Calendar::class, CalendarImage::class, Event::class, Image::class => $this->addUserFilter($qb),
            User::class => $this->addUserFilter($qb, true),

            /* Disable classes for user */
            CalendarStyle::class, Holiday::class, HolidayGroup::class => $this->checkPermissions($qb, $this->getEntity()),

            /* Do not filter */
            default => $qb,
        };
    }

    /**
     * Configures filters.
     *
     * @param Filters $filters
     * @return Filters
     * @throws Exception
     */
    public function configureFilters(Filters $filters): Filters
    {
        $filterFields = $this->getConstant('CRUD_FIELDS_FILTER');

        foreach ($filterFields as $filterField) {
            $filters->add($filterField);
        }

        return $filters;
    }

    /**
     * Set icon name.
     *
     * @param Actions $actions
     * @param string $pageName
     * @param string $actionName
     * @param string $icon
     * @return void
     */
    protected function setIcon(Actions $actions, string $pageName, string $actionName, string $icon): void
    {
        $actions->getAsDto($pageName)->getAction($pageName, $actionName)?->setIcon($icon);
    }
}
