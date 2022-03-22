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

use App\Controller\Base\BaseController;
use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\HolidayGroup;
use App\Service\CalendarSheetCreateService;
use App\Service\ImageService;
use App\Service\UrlService;
use chillerlan\QRCode\QRCode;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatableMessage;

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

    protected CalendarSheetCreateService $calendarSheetCreateService;

    protected RequestStack $requestStack;

    protected UrlGeneratorInterface $router;

    /**
     * EasyAdminSubscriber constructor.
     *
     * @param ImageService $imageService
     * @param CalendarSheetCreateService $calendarSheetCreateService
     * @param RequestStack $requestStack
     * @param UrlGeneratorInterface $router
     */
    public function __construct(ImageService $imageService, CalendarSheetCreateService $calendarSheetCreateService, RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->imageService = $imageService;

        $this->calendarSheetCreateService = $calendarSheetCreateService;

        $this->requestStack = $requestStack;

        $this->router = $router;
    }

    /**
     * Subscribes all easy admin events.
     *
     * @return string[][]
     */
    #[ArrayShape([BeforeEntityPersistedEvent::class => "string[]", BeforeEntityUpdatedEvent::class => "string[]", AfterEntityPersistedEvent::class => "string[]", AfterEntityUpdatedEvent::class => "string[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addLinkCalendarImagePersisted'],
            BeforeEntityUpdatedEvent::class => ['addLinkCalendarImageUpdated'],
            AfterEntityPersistedEvent::class => ['createCalendarImagePersisted'],
            AfterEntityUpdatedEvent::class => ['createCalendarImageUpdated'],
        ];
    }

    /**
     * Adds a flash message to the current session for type.
     *
     * @param string $type
     * @param mixed $message
     * @throws Exception
     */
    protected function addFlash(string $type, mixed $message): void
    {
        $session = $this->requestStack->getSession();

        /* @phpstan-ignore-next-line */
        $flashBag = $session->getFlashBag();

        if (!$flashBag instanceof FlashBag) {
            throw new Exception(sprintf('Unable to get flash bag (%s:%d).', __FILE__, __LINE__));
        }

        $flashBag->add($type, $message);
    }

    /**
     * Builds target image.
     *
     * @param CalendarImage $calendarImage
     * @return bool
     * @throws Exception
     */
    protected function buildTargetImage(CalendarImage $calendarImage): bool
    {
        $calendar = $calendarImage->getCalendar();

        if ($calendar === null) {
            throw new Exception(sprintf('Calendar class not found (%s:%d).', __FILE__, __LINE__));
        }

        $holidayGroup = $calendar->getHolidayGroup();

        if (!$holidayGroup instanceof HolidayGroup) {
            throw new Exception(sprintf('Unable to get holiday group (%s:%d).', __FILE__, __LINE__));
        }

        $data = $this->calendarSheetCreateService->create($calendarImage, $holidayGroup, QRCode::VERSION_AUTO);

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

        return true;
    }

    /**
     * Gets encoded string from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @param bool $short
     * @return string
     * @throws Exception
     */
    protected function getEncoded(CalendarImage $calendarImage, bool $short = false): string
    {
        $calendar = $calendarImage->getCalendar();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Unable to get calendar (%s:%d).', __FILE__, __LINE__));
        }

        return match ($calendarImage->getMonth()) {
            0 => UrlService::encode(BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => $short ? $calendar->getUser()->getIdHashShort() : $calendar->getUser()->getIdHash(),
                'userId' => intval($calendar->getUser()->getId()),
                'calendarId' => $calendar->getId(),
            ], false, true),
            default => UrlService::encode(BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => $short ? $calendar->getUser()->getIdHashShort() : $calendar->getUser()->getIdHash(),
                'userId' => intval($calendar->getUser()->getId()),
                'calendarImageId' => $calendarImage->getId(),
            ], false, true),
        };
    }

    /**
     * Returns the full and automatically generated URL from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @param string $defaultHost
     * @return string
     * @throws Exception
     */
    protected function getUrl(CalendarImage $calendarImage, string $defaultHost = 'twelvepics.com'): string
    {
        $calendar = $calendarImage->getCalendar();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Unable to get calendar (%s:%d).', __FILE__, __LINE__));
        }

        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        $host = in_array($currentRequest->getHost(), ['localhost', '127.0.0.1']) ? $defaultHost : $currentRequest->getHost();

        $encoded = $this->getEncoded($calendarImage, true);

        $path = match ($calendarImage->getMonth()) {
            0 => $this->router->generate(BaseController::ROUTE_NAME_APP_CALENDAR_INDEX_ENCODED_SHORT, [
                'encoded' => $encoded,
            ]),
            default => $this->router->generate(BaseController::ROUTE_NAME_APP_CALENDAR_DETAIL_ENCODED_SHORT, [
                'encoded' => $encoded,
            ]),
        };

        return sprintf('https://%s%s', $host, $path);
    }

    /**
     * Adds url to calendar image if url is empty.
     *
     * @param CalendarImage $calendarImage
     * @return bool
     * @throws Exception
     */
    protected function addUrl(CalendarImage $calendarImage): bool
    {
        if (!empty($calendarImage->getUrl())) {
            return true;
        }

        /* Set new url to calendar image. */
        $calendarImage->setUrl($this->getUrl($calendarImage));

        return true;
    }

    /**
     * Create calendar sheet (create).
     *
     * @param AfterEntityPersistedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function createCalendarImagePersisted(AfterEntityPersistedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->buildTargetImage($entity);
    }

    /**
     * Create calendar sheet (update).
     *
     * @param AfterEntityUpdatedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function createCalendarImageUpdated(AfterEntityUpdatedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->buildTargetImage($entity);
    }

    public function addLinkCalendarImagePersisted(BeforeEntityPersistedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->addUrl($entity);
    }

    public function addLinkCalendarImageUpdated(BeforeEntityUpdatedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->addUrl($entity);
    }
}
