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
use App\Service\Entity\CalendarLoaderService;
use App\Service\Entity\ImageLoaderService;
use App\Service\Entity\UserLoaderService;
use App\Service\SecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Exception;
use JetBrains\PhpStorm\Pure;
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

    /**
     * CalendarImageCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param CalendarLoaderService $calendarLoaderService
     * @param ImageLoaderService $imageLoaderService
     * @param UserLoaderService $userLoaderService
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, CalendarLoaderService $calendarLoaderService, ImageLoaderService $imageLoaderService, UserLoaderService $userLoaderService)
    {
        $this->calendarLoaderService = $calendarLoaderService;

        $this->imageLoaderService = $imageLoaderService;

        $this->userLoaderService = $userLoaderService;

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
}
