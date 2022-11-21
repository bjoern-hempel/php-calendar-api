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

use App\Config\SearchConfig;
use App\Controller\Base\BaseController;
use App\Service\Entity\PlaceLoaderService;
use App\Service\LocationDataService;
use App\Service\VersionService;
use App\Utils\Timer;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContentController
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-23)
 * @package App\Controller
 */
class ContentController extends BaseController
{
    /**
     * ContentController constructor.
     *
     * @param LocationDataService $locationDataService
     * @param TranslatorInterface $translator
     * @param KernelInterface $kernel
     * @param VersionService $versionService
     * @param PlaceLoaderService $placeLoaderService
     * @param SearchConfig $searchConfig
     */
    public function __construct(protected LocationDataService $locationDataService, protected TranslatorInterface $translator, protected KernelInterface $kernel, protected VersionService $versionService, protected PlaceLoaderService $placeLoaderService, protected SearchConfig $searchConfig)
    {
    }

    /**
     * Index route.
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: BaseController::ROUTE_NAME_APP_INDEX)]
    public function index(): Response
    {
        return $this->render('content/index.html.twig');
    }

    /**
     * Impress route.
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/impress', name: BaseController::ROUTE_NAME_APP_IMPRESS)]
    public function impress(): Response
    {
        return $this->render('content/impress.html.twig');
    }

    /**
     * Location route.
     *
     * @return Response
     * @throws Exception
     */
    #[Route('/location', name: BaseController::ROUTE_NAME_APP_LOCATION)]
    public function location(): Response
    {
        return match (true) {
            $this->searchConfig->getViewMode() === SearchConfig::VIEW_MODE_SEARCH => $this->locationSearch(),
            $this->searchConfig->getViewMode() === SearchConfig::VIEW_MODE_LIST => $this->locationList(),
            $this->searchConfig->getViewMode() === SearchConfig::VIEW_MODE_DETAIL => $this->locationDetail(),
            $this->searchConfig->getViewMode() === SearchConfig::VIEW_MODE_CURRENT_POSITION => $this->locationDetail(),
            default => throw new Exception(sprintf('Unsupported state (%s:%d).', __FILE__, __LINE__)),
        };
    }

    /**
     * Location view route.
     *
     * @param string $param1
     * @param string $param2
     * @return Response
     * @throws Exception
     */
    #[Route('/location/{param1}/{param2}', name: BaseController::ROUTE_NAME_APP_LOCATION_VIEW)]
    public function locationView(string $param1, string $param2): Response
    {
        if (in_array($param1, ['a', 'h', 'l', 'p', 'r', 's', 't', 'u', 'v', ])) {
            $this->searchConfig->setIdString(sprintf('%s:%s', $param1, $param2));
        } else {
            $this->searchConfig->setLocation([floatval($param1), floatval($param2)]);
        }

        return $this->locationDetail();
    }

    /**
     * Location search.
     *
     * @return Response
     * @throws Exception
     */
    protected function locationSearch(): Response
    {
        $timer = Timer::start();
        $time = Timer::stop($timer);

        return $this->renderForm('content/location/search.html.twig', [
            'searchConfig' => $this->searchConfig,
            'version' => $this->versionService->getVersion(),
            'time' => $time,
        ]);
    }

    /**
     * Location list.
     *
     * @return Response
     * @throws Exception
     */
    protected function locationList(): Response
    {
        $timer = Timer::start();

        $search = $this->searchConfig->getSearchQuery();

        if (is_null($search)) {
            throw new Exception(sprintf('No search query was given (%s:%d).', __FILE__, __LINE__));
        }

        $locationListResults = $this->locationDataService->getLocationListResults($search, true);

        $results = $locationListResults['results'];
        $numberResults = $locationListResults['numberResults'];
        $error = $locationListResults['error'];

        if (!is_null($error)) {
            $this->searchConfig->setError($error);
            return $this->locationSearch();
        }

        $this->searchConfig->setNumberResults($numberResults);

        $time = Timer::stop($timer);

        return $this->renderForm('content/location/list.html.twig', [
            'searchConfig' => $this->searchConfig,
            'results' => $results,
            'version' => $this->versionService->getVersion(),
            'time' => $time,
        ]);
    }

    /**
     * Location detail / Location current location search.
     *
     * @return Response
     * @throws Exception
     */
    protected function locationDetail(): Response
    {
        $timer = Timer::start();

        $locationDetailData = match (true) {
            $this->searchConfig->hasIdString() => $this->locationDataService->getLocationDetailDataFromIdString(!is_null($this->searchConfig->getIdString()) ? $this->searchConfig->getIdString() : ''),
            $this->searchConfig->hasLocation() => $this->locationDataService->getLocationDetailDataFromLocation(!is_null($this->searchConfig->getLocationString()) ? $this->searchConfig->getLocationString() : ''),
            default => throw new Exception(sprintf('Unsupported mode (%s:%d).', __FILE__, __LINE__)),
        };

        $locationData = $locationDetailData['locationData'];
        $error = $locationDetailData['error'];

        if (!is_null($error)) {
            $this->searchConfig->setError($error);
            return $this->locationSearch();
        }

        if (array_key_exists(LocationDataService::KEY_NAME_PLACES_NEAR, $locationData)) {
            $placesNear = $locationData[LocationDataService::KEY_NAME_PLACES_NEAR];
            unset($locationData[LocationDataService::KEY_NAME_PLACES_NEAR]);
        } else {
            $placesNear = [];
        }

        $time = Timer::stop($timer);

        return $this->renderForm('content/location/detail.html.twig', [
            'searchConfig' => $this->searchConfig,
            'currentSearch' => $this->searchConfig->hasLocation(),
            'locationData' => $locationData,
            'placesNear' => $placesNear,
            'version' => $this->versionService->getVersion(),
            'time' => $time,
        ]);
    }
}
