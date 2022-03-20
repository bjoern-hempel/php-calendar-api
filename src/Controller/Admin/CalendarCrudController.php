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
use App\Controller\Base\BaseController;
use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\HolidayGroup;
use App\Service\CalendarSheetCreateService;
use App\Service\SecurityService;
use App\Service\UrlService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CalendarCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin
 */
class CalendarCrudController extends BaseCrudController
{
    public const ACTION_BUILD_CALENDAR_SHEETS = 'buildCalendarSheets';

    public const ACTION_VIEW_CALENDAR_SHEETS = 'viewCalendarSheets';

    protected CalendarSheetCreateService $calendarSheetCreateService;

    /**
     * CalendarCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param CalendarSheetCreateService $calendarSheetCreateService
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, CalendarSheetCreateService $calendarSheetCreateService)
    {
        $this->calendarSheetCreateService = $calendarSheetCreateService;

        parent::__construct($securityService, $translator);
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
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

        $buildCalendarSheets = Action::new(self::ACTION_BUILD_CALENDAR_SHEETS, 'admin.calendar.fields.buildCalendarSheets.label', 'fa fa-refresh')
            ->linkToCrudAction(self::ACTION_BUILD_CALENDAR_SHEETS)
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-sheets',
            ]);

        $viewCalendarSheets = Action::new(self::ACTION_VIEW_CALENDAR_SHEETS, 'admin.calendar.fields.viewCalendarSheets.label', 'fa fa-calendar')
            ->linkToCrudAction(self::ACTION_VIEW_CALENDAR_SHEETS)
            ->setHtmlAttributes([
                'target' => '_blank',
            ]);

        $actions
            ->add(Crud::PAGE_DETAIL, $buildCalendarSheets)
            ->add(Crud::PAGE_INDEX, $buildCalendarSheets)
            ->add(Crud::PAGE_DETAIL, $viewCalendarSheets)
            ->add(Crud::PAGE_INDEX, $viewCalendarSheets)
            ->reorder(Crud::PAGE_INDEX, [
                Action::DETAIL,
                Action::EDIT,
                Action::DELETE,
                self::ACTION_VIEW_CALENDAR_SHEETS,
                self::ACTION_BUILD_CALENDAR_SHEETS,
            ]);

        $this->setIcon($actions, Crud::PAGE_INDEX, Action::DETAIL, 'fa fa-eye');
        $this->setIcon($actions, Crud::PAGE_INDEX, Action::EDIT, 'fa fa-edit');
        $this->setIcon($actions, Crud::PAGE_INDEX, Action::DELETE, 'fa fa-eraser');

        return $actions;
    }

    /**
     * Gets the calendar from AdminContext.
     *
     * @param AdminContext $context
     * @return Calendar
     * @throws Exception
     */
    protected function getCalendar(AdminContext $context): Calendar
    {
        /** @var Calendar $calendar */
        $calendar = $context->getEntity()->getInstance();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Calendar class of instance expected (%s:%d).', __FILE__, __LINE__));
        }

        return $calendar;
    }

    /**
     * Build calendar sheet.
     *
     * @param CalendarImage $calendarImage
     * @param HolidayGroup $holidayGroup
     * @return float[]|array<string|int>[]
     * @throws Exception
     */
    #[ArrayShape(['file' => "mixed", 'time' => "float"])]
    protected function buildCalendarSheet(CalendarImage $calendarImage, HolidayGroup $holidayGroup): array
    {
        $calendar = $calendarImage->getCalendar();

        if ($calendar === null) {
            throw new Exception(sprintf('Calendar class not found (%s:%d).', __FILE__, __LINE__));
        }

        return $this->calendarSheetCreateService->create($calendarImage, $holidayGroup);
    }

    /**
     * Views all calendar sheet.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws Exception
     */
    public function viewCalendarSheets(AdminContext $context): RedirectResponse
    {
        $calendar = $this->getCalendar($context);

        $encoded = UrlService::encode(BaseController::CONFIG_APP_CALENDAR_INDEX, [
            'hash' => $calendar->getUser()->getIdHash(),
            'userId' => intval($calendar->getUser()->getId()),
            'calendarId' => $calendar->getId(),
        ]);

        return $this->redirectToRoute(BaseController::ROUTE_NAME_APP_CALENDAR_INDEX_ENCODED, [
            'encoded' => $encoded,
        ]);
    }

    /**
     * Build calendar sheet.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws Exception
     */
    public function buildCalendarSheets(AdminContext $context): RedirectResponse
    {
        $calendar = $this->getCalendar($context);

        /* Some parameters */
        $time = 0;
        $number = 0;
        $sizes = [];

        $holidayGroup = $calendar->getHolidayGroup();

        if (!$holidayGroup instanceof HolidayGroup) {
            throw new Exception(sprintf('Unable to get holiday group (%s:%d).', __FILE__, __LINE__));
        }

        foreach ($calendar->getCalendarImages() as $calendarImage) {
            $data = $this->buildCalendarSheet($calendarImage, $holidayGroup);

            $file = $data['file'];
            $time += floatval($data['time']);
            $number++;

            if (!is_array($file)) {
                throw new Exception(sprintf('Array expected (%s:%d).', __FILE__, __LINE__));
            }

            $sizes[] = $file['sizeHumanTarget'];
        }

        $this->addFlash('success', new TranslatableMessage('admin.actions.calendarSheets.success', [
            '%name%' => $calendar->getName(),
            '%number%' => $number,
            '%time%' => sprintf('%.2f', $time),
            '%sizes%' => implode(', ', $sizes),
        ]));

        $referrer = $context->getReferrer();

        if ($referrer === null) {
            throw new Exception(sprintf('Unable to get referrer (%s:%d).', __FILE__, __LINE__));
        }

        return $this->redirect($referrer);
    }
}
