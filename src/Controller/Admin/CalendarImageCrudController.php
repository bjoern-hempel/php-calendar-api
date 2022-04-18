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
use App\Entity\HolidayGroup;
use App\Entity\Image;
use App\Service\CalendarSheetCreateService;
use App\Service\Entity\CalendarLoaderService;
use App\Service\Entity\ImageLoaderService;
use App\Service\Entity\UserLoaderService;
use App\Service\SecurityService;
use App\Service\UrlService;
use App\Utils\FileNameConverter;
use chillerlan\QRCode\QRCode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
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

    protected CalendarSheetCreateService $calendarSheetCreateService;

    protected RequestStack $requestStack;

    protected UrlService $urlService;

    protected EntityManagerInterface $manager;

    public const ACTION_BUILD_CALENDAR_SHEET = 'buildCalendarSheet';

    public const ACTION_REBUILD_URL = 'rebuildUrl';

    public const PARAMETER_CALENDAR = 'calendar';

    /**
     * CalendarImageCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param CalendarLoaderService $calendarLoaderService
     * @param ImageLoaderService $imageLoaderService
     * @param UserLoaderService $userLoaderService
     * @param CalendarSheetCreateService $calendarSheetCreateService
     * @param RequestStack $requestStack
     * @param UrlService $urlService
     * @param EntityManagerInterface $manager
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, CalendarLoaderService $calendarLoaderService, ImageLoaderService $imageLoaderService, UserLoaderService $userLoaderService, CalendarSheetCreateService $calendarSheetCreateService, RequestStack $requestStack, UrlService $urlService, EntityManagerInterface $manager)
    {
        parent::__construct($securityService, $translator);

        $this->calendarLoaderService = $calendarLoaderService;

        $this->imageLoaderService = $imageLoaderService;

        $this->userLoaderService = $userLoaderService;

        $this->calendarSheetCreateService = $calendarSheetCreateService;

        $this->requestStack = $requestStack;

        $this->urlService = $urlService;

        $this->manager = $manager;
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
     * Returns month selection.
     *
     * @return int[]
     */
    protected function getMonthSelection(): array
    {
        return [
            'title' => 0,
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];
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
        $previewWidth = 100;

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
                ->renderAsNativeWidget()
                ->setFormTypeOption('choices', $this->imageLoaderService->loadImages())
                ->setFormTypeOption('expanded', true)
                ->setFormTypeOption('label_html', true)
                ->setFormTypeOption(
                    'choice_label',
                    function (Image $image) use ($previewWidth) {
                        return sprintf(
                            '<p><img class="preview" src="%s" width="%d" title="%s" alt="%s" data-title> &nbsp; %s</p>',
                            $image->getPath(outputMode: FileNameConverter::MODE_OUTPUT_RELATIVE, width: $previewWidth),
                            $previewWidth,
                            $image->getName(),
                            $image->getName(),
                            $image->getName()
                        );
                    }
                )
                ->setRequired(true)
                ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'year' => $this->easyAdminField->getChoiceField($fieldName, $this->getYearSelection()),
            'month' => $this->easyAdminField->getChoiceField($fieldName, $this->getMonthSelection(), true),
            'pathSource', 'pathTarget' => ImageField::new($fieldName)
                    //->setBasePath(sprintf('%s/%s', Image::PATH_DATA, Image::PATH_IMAGES))
                    ->setTemplatePath('admin/crud/field/image_preview.html.twig')
                    ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
                    ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName)),
            'pathSourcePreview', 'pathTargetPreview' => ImageField::new($fieldName)
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

        $buildCalendarSheet = Action::new(self::ACTION_BUILD_CALENDAR_SHEET, 'admin.calendarImage.fields.buildCalendarSheet.label', 'fa fa-calendar-alt')
            ->linkToCrudAction(self::ACTION_BUILD_CALENDAR_SHEET)
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-sheet',
            ]);

        $rebuildUrl = Action::new(self::ACTION_REBUILD_URL, 'admin.calendarImage.fields.rebuildUrl.label', 'fa fa-calendar-alt')
            ->linkToCrudAction(self::ACTION_REBUILD_URL)
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-calendar-sheet',
            ]);

        $actions
            ->add(Crud::PAGE_DETAIL, $buildCalendarSheet)
            ->add(Crud::PAGE_DETAIL, $rebuildUrl)
            ->add(Crud::PAGE_INDEX, $buildCalendarSheet)
            ->add(Crud::PAGE_INDEX, $rebuildUrl)
            ->reorder(Crud::PAGE_INDEX, [
                Action::DETAIL,
                Action::EDIT,
                self::ACTION_BUILD_CALENDAR_SHEET,
                self::ACTION_REBUILD_URL,
                Action::DELETE
            ]);

        $this->setIcon($actions, Crud::PAGE_INDEX, Action::DETAIL, 'fa fa-eye');
        $this->setIcon($actions, Crud::PAGE_INDEX, Action::EDIT, 'fa fa-edit');
        $this->setIcon($actions, Crud::PAGE_INDEX, Action::DELETE, 'fa fa-eraser');

        return $actions;
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
        $crud = parent::configureCrud($crud);

        $crud->setDefaultSort([
            'calendar' => 'ASC',
            'month' => 'ASC'
        ]);

        return $crud;
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

        $holidayGroup = $calendar->getHolidayGroup();

        if (!$holidayGroup instanceof HolidayGroup) {
            throw new Exception(sprintf('Unable to get holiday group (%s:%d).', __FILE__, __LINE__));
        }

        $data = $this->calendarSheetCreateService->create($calendarImage, $holidayGroup, QRCode::VERSION_AUTO, true);

        $file = $data['file'];
        $time = floatval($data['time']);

        if (!is_array($file)) {
            throw new Exception(sprintf('Array expected (%s:%d).', __FILE__, __LINE__));
        }

        $this->addFlash('success', new TranslatableMessage('admin.actions.calendarSheet.success', [
            '%month%' => $calendarImage->getMonth(),
            '%year%' => $calendarImage->getYear(),
            '%calendar%' => $calendar->getTitle(),
            '%file%' => $file['pathRelativeTarget'],
            '%width%' => $file['widthTarget'],
            '%height%' => $file['heightTarget'],
            '%size%' => $file['sizeHumanTarget'],
            '%time%' => sprintf('%.2f', $time),
        ]));

        $referrer = $context->getReferrer();

        if ($referrer === null) {
            throw new Exception(sprintf('Unable to get referrer (%s:%d).', __FILE__, __LINE__));
        }

        return $this->redirect($referrer);
    }

    /**
     * Rebuilds the url.
     *
     * @param AdminContext $context
     * @return RedirectResponse
     * @throws Exception
     */
    public function rebuildUrl(AdminContext $context): RedirectResponse
    {
        /** @var CalendarImage $calendarImage */
        $calendarImage = $context->getEntity()->getInstance();

        if (!$calendarImage instanceof CalendarImage) {
            throw new Exception(sprintf('CalendarImage class of instance expected (%s:%d).', __FILE__, __LINE__));
        }

        /* Set new url to calendar image. */
        $calendarImage->setUrl($this->urlService->getUrl($calendarImage));
        $this->manager->persist($calendarImage);
        $this->manager->flush();

        return $this->buildCalendarSheet($context);
    }

    /**
     * Set default settings from
     *
     * @param string $entityFqcn
     * @return CalendarImage
     * @throws Exception
     */
    public function createEntity(string $entityFqcn): CalendarImage
    {
        /** @var CalendarImage $calendarImage */
        $calendarImage = new $entityFqcn();

        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        $query = $currentRequest->query;

        if ($query->has(self::PARAMETER_CALENDAR)) {
            $calendarId = intval($query->get(self::PARAMETER_CALENDAR));

            $calendar = $this->calendarLoaderService->findOneById($calendarId);

            $calendarImage->setCalendar($calendar);
            $calendarImage->setYear($calendar->getDefaultYear());
        }

        return $calendarImage;
    }
}
