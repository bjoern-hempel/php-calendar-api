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

use App\Service\ImageService;
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
            AfterEntityBuiltEvent::class => ['checkImage'],
        ];
    }

    /**
     * Checks image directories.
     *
     * @param AfterEntityBuiltEvent $event
     * @throws Exception
     */
    public function checkImage(AfterEntityBuiltEvent $event): void
    {
        $this->imageService->checkImage($event);
    }
}
