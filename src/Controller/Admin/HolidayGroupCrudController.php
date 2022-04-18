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

use App\Controller\Admin\Base\BaseCrudController;
use App\Entity\HolidayGroup;
use App\Service\SecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HolidayGroupCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-10)
 * @package App\Controller\Admin
 */
class HolidayGroupCrudController extends BaseCrudController
{
    protected AdminUrlGenerator $adminUrlGenerator;

    public const ACTION_NEW_HOLIDAY = 'newHoliday';

    /**
     * HolidayGroupCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param AdminUrlGenerator $adminUrlGenerator
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, AdminUrlGenerator $adminUrlGenerator)
    {
        parent::__construct($securityService, $translator);

        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return HolidayGroup::class;
    }

    /**
     * Returns the entity of this class.
     *
     * @return string
     */
    #[Pure]
    public function getEntity(): string
    {
        return self::getEntityFqcn();
    }

    /**
     * Configure actions.
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $newHoliday = Action::new(self::ACTION_NEW_HOLIDAY, 'admin.holidayGroup.fields.newHoliday.label', 'fa fa-plus-square-o')
            ->linkToCrudAction(self::ACTION_NEW_HOLIDAY)
            ->setCssClass('action-new btn btn-primary');

        $actions
            ->add(Crud::PAGE_DETAIL, $newHoliday)
            ->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::DELETE, self::ACTION_NEW_HOLIDAY, Action::EDIT]);

        return $actions;
    }

    /**
     * New holiday.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     */
    public function newHoliday(AdminContext $context): RedirectResponse
    {
        /** @var HolidayGroup $holidayGroup */
        $holidayGroup = $context->getEntity()->getInstance();

        $url = $this->adminUrlGenerator
            ->setController(HolidayCrudController::class)
            ->setAction(Action::NEW)
            ->set(HolidayCrudController::PARAMETER_HOLIDAY_GROUP, $holidayGroup->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
