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

use App\Entity\CalendarImage;
use App\Entity\HolidayGroup;
use App\Service\CalendarSheetCreateService;
use App\Service\ImageService;
use App\Service\UrlService;
use chillerlan\QRCode\QRCode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Class EasyAdminSubscriber.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-04-18)
 * @since 1.0.1 (2022-04-18) EasyAdmin: Possibility to choose the images directly in the calendar (#88)
 * @since 1.0.0 (2022-02-26) First version.
 * @package App\EventSubscriber
 */
class EasyAdminSubscriber implements EventSubscriberInterface
{
    protected ImageService $imageService;

    protected CalendarSheetCreateService $calendarSheetCreateService;

    protected RequestStack $requestStack;

    protected UrlService $urlService;

    protected EntityManagerInterface $manager;

    /**
     * EasyAdminSubscriber constructor.
     *
     * @param ImageService $imageService
     * @param CalendarSheetCreateService $calendarSheetCreateService
     * @param RequestStack $requestStack
     * @param UrlService $urlService
     * @param EntityManagerInterface $manager
     */
    public function __construct(ImageService $imageService, CalendarSheetCreateService $calendarSheetCreateService, RequestStack $requestStack, UrlService $urlService, EntityManagerInterface $manager)
    {
        $this->imageService = $imageService;

        $this->calendarSheetCreateService = $calendarSheetCreateService;

        $this->requestStack = $requestStack;

        $this->urlService = $urlService;

        $this->manager = $manager;
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
            AfterEntityPersistedEvent::class => ['doAfterEntityPersistedEvent'],
            AfterEntityUpdatedEvent::class => ['doAfterEntityUpdatedEvent'],
            BeforeEntityPersistedEvent::class => ['doBeforeEntityPersistedEvent'],
            BeforeEntityUpdatedEvent::class => ['doBeforeEntityUpdatedEvent'],
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

        return true;
    }

    /**
     * Adds url to calendar image if url is empty.
     *
     * @param CalendarImage $calendarImage
     * @param bool $persist
     * @return bool
     * @throws Exception
     */
    protected function addUrl(CalendarImage $calendarImage, bool $persist = false): bool
    {
        if (!empty($calendarImage->getUrl())) {
            return true;
        }

        /* Set new url to calendar image. */
        $calendarImage->setUrl($this->urlService->getUrl($calendarImage));

        /* Persist calendar image if needed. */
        if ($persist) {
            $this->manager->persist($calendarImage);
            $this->manager->flush();
        }

        return true;
    }

    /**
     * Creates calendar sheet (create) and update url.
     *
     * @param AfterEntityPersistedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function doAfterEntityPersistedEvent(AfterEntityPersistedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->addUrl($entity, true);

        $this->buildTargetImage($entity);
    }

    /**
     * Creates calendar sheet (update) and update url.
     *
     * @param AfterEntityUpdatedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function doAfterEntityUpdatedEvent(AfterEntityUpdatedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        $this->addUrl($entity, true);

        $this->buildTargetImage($entity);
    }

    /**
     * Make changes to calendar image if entity is persisted.
     *
     * @param BeforeEntityPersistedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function doBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        // Todo: Do something with $entity
    }

    /**
     * Make changes to calendar image if entity is updated.
     *
     * @param BeforeEntityUpdatedEvent $entityInstance
     * @return void
     * @throws Exception
     */
    public function doBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $entityInstance): void
    {
        $entity = $entityInstance->getEntityInstance();

        if (!$entity instanceof CalendarImage) {
            return;
        }

        // Todo: Do something with $entity
    }
}
