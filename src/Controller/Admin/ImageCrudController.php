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
use App\Entity\Image;
use App\Service\IdHashService;
use App\Service\ImageLoaderService;
use App\Service\ImageService;
use App\Service\SecurityService;
use App\Service\UserLoaderService;
use App\Utils\ImageProperty;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImageCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin
 */
class ImageCrudController extends BaseCrudController
{
    protected ImageProperty $imageProperty;

    protected ImageLoaderService $imageLoaderService;

    protected UserLoaderService $userLoaderService;

    protected RequestStack $requestStack;

    protected ImageService $imageService;

    protected IdHashService $idHashService;

    /**
     * UserCrudController constructor.
     *
     * @param ImageProperty $imageProperty
     * @param ImageLoaderService $imageLoaderService
     * @param UserLoaderService $userLoaderService
     * @param RequestStack $requestStack
     * @param ImageService $imageService
     * @param SecurityService $securityService
     * @param IdHashService $idHashService
     * @throws Exception
     */
    public function __construct(ImageProperty $imageProperty, ImageLoaderService $imageLoaderService, UserLoaderService $userLoaderService, RequestStack $requestStack, ImageService $imageService, IdHashService $idHashService, SecurityService $securityService, TranslatorInterface $translator)
    {
        $this->imageProperty = $imageProperty;

        $this->imageLoaderService = $imageLoaderService;

        $this->userLoaderService = $userLoaderService;

        $this->requestStack = $requestStack;

        $this->imageService = $imageService;

        $this->idHashService = $idHashService;

        parent::__construct($securityService, $translator);
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Image::class;
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
        switch ($fieldName) {
            case 'path':
            case 'pathTarget':
                $idHash = $this->idHashService->getIdHash($this->getEntityInstance());

                /* Create source and target path if needed. */
                $this->imageService->checkPath($idHash);

                return ImageField::new($fieldName)
                    ->setBasePath(sprintf('%s/%s', Image::PATH_DATA, Image::PATH_IMAGES))
                    ->setUploadDir(sprintf('%s/%s/%s/%s', Image::PATH_DATA, Image::PATH_IMAGES, $idHash, Image::PATH_TYPE_SOURCE))
                    ->setUploadedFileNamePattern(
                        function (UploadedFile $file) use ($idHash) {
                            return sprintf('%s/%s/%s', $idHash, Image::PATH_TYPE_SOURCE, $file->getClientOriginalName());
                        }
                    );
        }

        return parent::getField($fieldName);
    }

    /**
     * Updates the image properties
     *
     * @param Image $image
     * @throws Exception
     */
    protected function updateImageProperties(Image $image): void
    {
        if (!$this->securityService->isGrantedByAnAdmin()) {
            $image->setUser($this->securityService->getUser());
        }

        if ($image->getUser() === null) {
            throw new Exception(sprintf('User expected (%s:%d).', __FILE__, __LINE__));
        }

        $this->imageProperty->init($image->getUser(), $image);
    }

    /**
     * Overwrite persistEntity method.
     *
     * @param EntityManagerInterface $entityManager
     * @param Image $entityInstance
     * @throws Exception
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            throw new Exception(sprintf('Unexpected entity class (%s:%d)', __FILE__, __LINE__));
        }

        $image = $entityInstance;

        $this->updateImageProperties($image);

        parent::persistEntity($entityManager, $image);
    }

    /**
     * Overwrite updateEntity method.
     *
     * @param EntityManagerInterface $entityManager
     * @param Image $entityInstance
     * @throws Exception
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Image) {
            throw new Exception(sprintf('Unexpected entity class (%s:%d)', __FILE__, __LINE__));
        }

        $image = $entityInstance;

        $this->updateImageProperties($image);

        parent::updateEntity($entityManager, $image);
    }
}
