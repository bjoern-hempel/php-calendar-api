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
use App\Entity\CalendarImage;
use App\Entity\Image;
use App\Service\CalendarBuilderService;
use App\Service\Entity\CalendarLoaderService;
use App\Service\Entity\HolidayGroupLoaderService;
use App\Service\Entity\ImageLoaderService;
use App\Service\Entity\UserLoaderService;
use App\Service\SecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CalendarImageCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin
 */
class CalendarImageCrudController extends BaseCrudController
{
    protected CalendarLoaderService $calendarLoaderService;

    protected ImageLoaderService $imageLoaderService;

    protected UserLoaderService $userLoaderService;

    protected HolidayGroupLoaderService $holidayGroupLoaderService;

    protected KernelInterface $appKernel;

    /**
     * CalendarImageCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param CalendarLoaderService $calendarLoaderService
     * @param ImageLoaderService $imageLoaderService
     * @param UserLoaderService $userLoaderService
     * @param HolidayGroupLoaderService $holidayGroupLoaderService
     * @param KernelInterface $appKernel
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, CalendarLoaderService $calendarLoaderService, ImageLoaderService $imageLoaderService, UserLoaderService $userLoaderService, HolidayGroupLoaderService $holidayGroupLoaderService, KernelInterface $appKernel)
    {
        $this->calendarLoaderService = $calendarLoaderService;

        $this->imageLoaderService = $imageLoaderService;

        $this->userLoaderService = $userLoaderService;

        $this->holidayGroupLoaderService = $holidayGroupLoaderService;

        $this->appKernel = $appKernel;

        parent::__construct($securityService, $translator);
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return CalendarImage::class;
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
     * Returns the field by given name.
     *
     * @param string $fieldName
     * @return FieldInterface
     * @throws Exception
     */
    protected function getField(string $fieldName): FieldInterface
    {
        return match ($fieldName) {
            'user' => AssociationField::new($fieldName)
                ->setFormTypeOption('choices', $this->userLoaderService->loadUsers())
                ->setRequired(true)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'calendar' => AssociationField::new($fieldName)
                ->setFormTypeOption('choices', $this->calendarLoaderService->loadCalendars())
                ->setRequired(true)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'image' => AssociationField::new($fieldName)
                ->setFormTypeOption('choices', $this->imageLoaderService->loadImages())
                ->setRequired(true)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'year', 'month' => IntegerField::new($fieldName)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'pathSource', 'pathTarget', 'pathSource400', 'pathTarget400' => ImageField::new($fieldName)
                    ->setBasePath(sprintf('%s/%s', Image::PATH_DATA, Image::PATH_IMAGES))
                    ->setTemplatePath('admin/crud/field/image_preview.html.twig')
                    ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                    ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            default => parent::getField($fieldName),
        };
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

        $buildCalendarSheet = Action::new('buildCalendarSheet', 'admin.calendarImage.fields.buildCalendarSheet.label', 'fa fa-calendar-alt')
            ->linkToCrudAction('buildCalendarSheet')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-sheet',
                'id' => 'action-calendar-sheet'
            ]);

        $actions->add(Crud::PAGE_DETAIL, $buildCalendarSheet);

        return $actions;
    }

    /**
     * Build calendar sheet.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws Exception
     */
    public function buildCalendarSheet(AdminContext $context): RedirectResponse
    {
        /** @var CalendarImage $calendarImage */
        $calendarImage = $context->getEntity()->getInstance();

        if (!$calendarImage instanceof CalendarImage) {
            throw new Exception(sprintf('CalendarImage class of instance expected (%s:%d).', __FILE__, __LINE__));
        }

        $calendar = $calendarImage->getCalendar();

        if ($calendar === null) {
            throw new Exception(sprintf('Calendar class not found (%s:%d).', __FILE__, __LINE__));
        }

        /* Read parameters */
        $userId = $this->securityService->getUser()->getId();
        $holidayGroupName = 'Saxony';

        if ($userId === null) {
            throw new Exception(sprintf('Unable to find user id (%s:%d).', __FILE__, __LINE__));
        }

        /* Read calendar image and holiday group */
        $calendarImage = $this->calendarLoaderService->loadCalendarImage($userId, $calendar->getId(), $calendarImage->getYear(), $calendarImage->getMonth());
        $holidayGroup = $this->holidayGroupLoaderService->loadHolidayGroup($holidayGroupName);

        /* Create calendar image */
        $timeStart = microtime(true);
        $calendarBuilderService = new CalendarBuilderService($this->appKernel);
        $calendarBuilderService->init($calendarImage, $holidayGroup, false, true);
        $file = $calendarBuilderService->build();
        $timeTaken = microtime(true) - $timeStart;

        $this->addFlash('success', new TranslatableMessage('admin.actions.calendarSheet.success', [
            '%month%' => $calendarImage->getMonth(),
            '%year%' => $calendarImage->getYear(),
            '%calendar%' => $calendar->getTitle(),
            '%file%' => $file['pathRelativeTarget'],
            '%width%' => $file['widthTarget'],
            '%height%' => $file['heightTarget'],
            '%size%' => $file['sizeHumanTarget'],
            '%time%' => sprintf('%.2f', $timeTaken),
        ]));

        $referrer = $context->getReferrer();

        if ($referrer === null) {
            throw new Exception(sprintf('Unable to get referrer (%s:%d).', __FILE__, __LINE__));
        }

        return $this->redirect($referrer);
    }
}
