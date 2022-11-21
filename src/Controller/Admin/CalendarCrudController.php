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
use chillerlan\QRCode\QRCode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
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
    final public const ACTION_BUILD_CALENDAR_SHEETS = 'buildCalendarSheets';

    final public const ACTION_BUILD_CALENDAR_URLS = 'buildCalendarUrls';

    final public const ACTION_VIEW_CALENDAR_SHEETS = 'viewCalendarSheets';

    final public const ACTION_NEW_CALENDAR_IMAGE = 'newCalendarImage';

    /**
     * CalendarCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param CalendarSheetCreateService $calendarSheetCreateService
     * @param UrlService $urlService
     * @param EntityManagerInterface $manager
     * @param AdminUrlGenerator $adminUrlGenerator
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, protected CalendarSheetCreateService $calendarSheetCreateService, protected UrlService $urlService, protected EntityManagerInterface $manager, protected AdminUrlGenerator $adminUrlGenerator)
    {
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

        $viewCalendarSheets = Action::new(self::ACTION_VIEW_CALENDAR_SHEETS, 'admin.calendar.fields.viewCalendarSheets.label', 'fa fa-calendar')
            ->linkToCrudAction(self::ACTION_VIEW_CALENDAR_SHEETS)
            ->setHtmlAttributes([
                'target' => '_blank',
            ]);

        $buildCalendarSheets = Action::new(self::ACTION_BUILD_CALENDAR_SHEETS, 'admin.calendar.fields.buildCalendarSheets.label', 'fa fa-refresh')
            ->linkToCrudAction(self::ACTION_BUILD_CALENDAR_SHEETS)
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-sheets',
            ]);

        $buildCalendarUrls = Action::new(self::ACTION_BUILD_CALENDAR_URLS, 'admin.calendar.fields.buildCalendarUrls.label', 'fa fa-link')
            ->linkToCrudAction(self::ACTION_BUILD_CALENDAR_URLS)
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-urls',
            ]);

        $newHoliday = Action::new(self::ACTION_NEW_CALENDAR_IMAGE, 'admin.calendar.fields.newCalendarImage.label', 'fa fa-plus-square-o')
            ->linkToCrudAction(self::ACTION_NEW_CALENDAR_IMAGE)
            ->setCssClass('action-new btn btn-primary');

        $actions
            ->add(Crud::PAGE_INDEX, $viewCalendarSheets)
            ->add(Crud::PAGE_DETAIL, $viewCalendarSheets)
            ->add(Crud::PAGE_INDEX, $buildCalendarSheets)
            ->add(Crud::PAGE_DETAIL, $buildCalendarSheets)
            ->add(Crud::PAGE_INDEX, $buildCalendarUrls)
            ->add(Crud::PAGE_DETAIL, $buildCalendarUrls)
            ->add(Crud::PAGE_DETAIL, $newHoliday)
            ->reorder(Crud::PAGE_INDEX, [
                Action::DETAIL,
                Action::EDIT,
                Action::DELETE,
                self::ACTION_VIEW_CALENDAR_SHEETS,
                self::ACTION_BUILD_CALENDAR_SHEETS,
                self::ACTION_BUILD_CALENDAR_URLS
            ])
            ->reorder(Crud::PAGE_DETAIL, [
                Action::INDEX,
                self::ACTION_VIEW_CALENDAR_SHEETS,
                self::ACTION_BUILD_CALENDAR_SHEETS,
                self::ACTION_BUILD_CALENDAR_URLS,
                Action::DELETE,
                self::ACTION_NEW_CALENDAR_IMAGE,
                Action::EDIT
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

        return $this->calendarSheetCreateService->create($calendarImage, $holidayGroup, QRCode::VERSION_AUTO, true);
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
     * Build calendar sheets.
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

    /**
     * Build calendar urls.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws Exception
     */
    public function buildCalendarUrls(AdminContext $context): RedirectResponse
    {
        $calendar = $this->getCalendar($context);

        $number = 0;

        $holidayGroup = $calendar->getHolidayGroup();

        if (!$holidayGroup instanceof HolidayGroup) {
            throw new Exception(sprintf('Unable to get holiday group (%s:%d).', __FILE__, __LINE__));
        }

        foreach ($calendar->getCalendarImages() as $calendarImage) {
            $number++;

            /* Build url */
            $url = $this->urlService->getUrl($calendarImage);

            /* Set url */
            $calendarImage->setUrl($url);

            /* Persist url. */
            $this->manager->persist($calendarImage);
        }

        /* Persists all urls */
        $this->manager->flush();

        $this->addFlash('success', new TranslatableMessage('admin.actions.calendarUrls.success', [
            '%name%' => $calendar->getName(),
            '%number%' => $number,
        ]));

        $referrer = $context->getReferrer();

        if ($referrer === null) {
            throw new Exception(sprintf('Unable to get referrer (%s:%d).', __FILE__, __LINE__));
        }

        return $this->redirect($referrer);
    }

    /**
     * New calendar image.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     */
    public function newCalendarImage(AdminContext $context): RedirectResponse
    {
        /** @var Calendar $calendar */
        $calendar = $context->getEntity()->getInstance();

        $url = $this->adminUrlGenerator
            ->setController(CalendarImageCrudController::class)
            ->setAction(Action::NEW)
            ->set(CalendarImageCrudController::PARAMETER_CALENDAR, $calendar->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
