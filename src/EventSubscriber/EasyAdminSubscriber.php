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

namespace App\EventSubscriber;

use App\Entity\Image;
use App\Service\ImageService;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EasyAdminSubscriber.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-26)
 * @package App\EventSubscriber
 */
class EasyAdminSubscriber implements EventSubscriberInterface
{
    protected ImageService $imageService;

    /**
     * EasyAdminSubscriber constructor.
     *
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Subscribes all easy admin events.
     *
     * @return string[][]
     */
    #[ArrayShape([AfterEntityBuiltEvent::class => "string[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityBuiltEvent::class => ['checkMissingImagesDetail'],
            AfterCrudActionEvent::class => ['checkMissingImagesList']
        ];
    }

    /**
     * Checks missing image on single Image instance.
     *
     * @param Image $image
     * @return void
     * @throws Exception
     */
    protected function checkMissingImages(Image $image): void
    {
        $this->imageService->createTargetImage($image);
        $this->imageService->createSourceImageWidth($image, Image::WIDTH_400);
        $this->imageService->createTargetImageWidth($image, Image::WIDTH_400);
    }

    /**
     * Checks target image (on image detail and edit page).
     *
     * @param AfterEntityBuiltEvent $event
     * @return void
     * @throws Exception
     */
    public function checkMissingImagesDetail(AfterEntityBuiltEvent $event): void
    {
        $instance = $event->getEntity()->getInstance();

        /* Checks if we are on image detail or detail page. */
        if (!$instance instanceof Image) {
            return;
        }

        $this->checkMissingImages($instance);
    }

    /**
     * Checks 400px width images (on image index page).
     *
     * @param AfterCrudActionEvent $event
     * @return void
     * @throws Exception
     */
    public function checkMissingImagesList(AfterCrudActionEvent $event): void
    {
        if (!$event->getResponseParameters()->has('entities')) {
            return;
        }

        /** @var EntityDto $entity */
        foreach ($event->getResponseParameters()->get('entities') as $entity) {
            $instance = $entity->getInstance();

            /* Checks if list element is an image element. */
            if (!$instance instanceof Image) {
                return;
            }

            $this->checkMissingImages($instance);
        }
    }
}
