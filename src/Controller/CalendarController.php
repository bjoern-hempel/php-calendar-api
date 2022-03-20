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

namespace App\Controller;

use App\Controller\Base\BaseController;
use App\Entity\Calendar;
use App\Repository\CalendarImageRepository;
use App\Service\Entity\CalendarLoaderService;
use App\Service\Entity\UserLoaderService;
use App\Service\SecurityService;
use App\Service\UrlService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CalendarController
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-18)
 * @package App\Controller
 */
class CalendarController extends BaseController
{
    protected UserLoaderService $userLoaderService;

    protected CalendarLoaderService $calendarLoaderService;

    protected CalendarImageRepository $calendarImageRepository;

    protected SecurityService $securityService;

    /**
     * CalendarController constructor.
     *
     * @param UserLoaderService $userLoaderService
     * @param CalendarLoaderService $calendarLoaderService
     * @param CalendarImageRepository $calendarImageRepository
     * @param SecurityService $securityService
     */
    public function __construct(UserLoaderService $userLoaderService, CalendarLoaderService $calendarLoaderService, CalendarImageRepository $calendarImageRepository, SecurityService $securityService)
    {
        $this->userLoaderService = $userLoaderService;

        $this->calendarLoaderService = $calendarLoaderService;

        $this->calendarImageRepository = $calendarImageRepository;

        $this->securityService = $securityService;
    }

    /**
     * This method checks, whether the user is allowed to see that given calendar.
     *
     * @param Calendar $calendar
     * @return bool
     * @throws Exception
     */
    protected function allowedToSeeCalendar(Calendar $calendar): bool
    {
        $published = $calendar->getPublished();

        $own = $this->securityService->isUserLoggedIn() && $this->securityService->getUser() === $calendar->getUser();

        $admin = $this->securityService->isGrantedByAnAdmin();

        return $published || $own || $admin;
    }

    /**
     * Index route.
     *
     * @param string $hash
     * @param int $userId
     * @param int $calendarId
     * @return Response
     * @throws Exception
     */
    #[Route('/calendar/{hash}/{userId}/{calendarId}', name: BaseController::ROUTE_NAME_APP_CALENDAR_INDEX)]
    public function index(string $hash, int $userId, int $calendarId): Response
    {
        $this->userLoaderService->loadUserCheckHash($userId, $hash);

        $calendar = $this->calendarLoaderService->loadCalendar($userId, $calendarId);

        if ($this->allowedToSeeCalendar($calendar)) {
            return $this->render('calendar/index.html.twig', [
                'calendar' => $calendar
            ]);
        }

        throw $this->createNotFoundException();
    }

    /**
     * Encoded index route.
     *
     * @param string $encoded
     * @return Response
     * @throws Exception
     */
    #[Route('/calendar/{encoded}', name: BaseController::ROUTE_NAME_APP_CALENDAR_INDEX_ENCODED)]
    public function indexEncoded(string $encoded): Response
    {
        $parameters = UrlService::decode(BaseController::CONFIG_APP_CALENDAR_INDEX, $encoded);

        $hash = strval($parameters['hash']);
        $userId = intval($parameters['userId']);
        $calendarId = intval($parameters['calendarId']);

        return $this->index($hash, $userId, $calendarId);
    }

    /**
     * Detail route.
     *
     * @param string $hash
     * @param int $userId
     * @param int $calendarImageId
     * @return Response
     * @throws Exception
     */
    #[Route('/calendar/detail/{hash}/{userId}/{calendarImageId}', name: BaseController::ROUTE_NAME_APP_CALENDAR_DETAIL)]
    public function detail(string $hash, int $userId, int $calendarImageId): Response
    {
        $this->userLoaderService->loadUserCheckHash($userId, $hash);

        $calendarImage = $this->calendarLoaderService->loadCalendarImageByUserHashAndCalendarImage($hash, $userId, $calendarImageId);

        $calendar = $calendarImage->getCalendar();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Unable to get calendar (%s:%d).', __FILE__, __LINE__));
        }

        if ($this->allowedToSeeCalendar($calendar)) {
            return $this->render('calendar/detail.html.twig', [
                'calendarImage' => $calendarImage
            ]);
        }

        throw $this->createNotFoundException();
    }

    /**
     * Encoded detail route.
     *
     * @param string $encoded
     * @return Response
     * @throws Exception
     */
    #[Route('/calendar/detail/{encoded}', name: BaseController::ROUTE_NAME_APP_CALENDAR_DETAIL_ENCODED)]
    public function detailEncoded(string $encoded): Response
    {
        $parameters = UrlService::decode(BaseController::CONFIG_APP_CALENDAR_DETAIL, $encoded);

        $hash = strval($parameters['hash']);
        $userId = intval($parameters['userId']);
        $calendarImageId = intval($parameters['calendarImageId']);

        return $this->detail($hash, $userId, $calendarImageId);
    }
}
