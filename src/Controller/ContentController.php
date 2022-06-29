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
use App\Entity\Place;
use App\Service\Entity\PlaceLoaderService;
use App\Service\LocationDataService;
use App\Service\VersionService;
use App\Utils\GPSConverter;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Class ContentController
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-23)
 * @package App\Controller
 */
class ContentController extends BaseController
{
    protected LocationDataService $locationDataService;

    protected TranslatorInterface $translator;

    protected KernelInterface $kernel;

    protected VersionService $versionService;

    protected PlaceLoaderService $placeLoaderService;

    public const PARAMETER_NAME_VERBOSE = 'v';

    public const PARAMETER_NAME_QUERY = 'q';

    public const PARAMETER_NAME_LOCATION = 'l';

    public const PARAMETER_NAME_PAGE = 'p';

    public const PARAMETER_NAME_SORT = 's';

    public const PARAMETER_NAME_ID = 'id';

    public const ORDER_BY_LOCATION = 'l';

    public const ORDER_BY_NAME = 'n';

    public const ORDER_BY_RELEVANCE = 'r';

    public const ORDER_BY_RELEVANCE_LOCATION = 'rl';

    public const NUMBER_PER_PAGE = 10;

    /**
     * ContentController constructor.
     *
     * @param LocationDataService $locationDataService
     * @param TranslatorInterface $translator
     * @param KernelInterface $kernel
     * @param VersionService $versionService
     * @param PlaceLoaderService $placeLoaderService
     */
    public function __construct(LocationDataService $locationDataService, TranslatorInterface $translator, KernelInterface $kernel, VersionService $versionService, PlaceLoaderService $placeLoaderService)
    {
        $this->locationDataService = $locationDataService;

        $this->translator = $translator;

        $this->kernel = $kernel;

        $this->versionService = $versionService;

        $this->placeLoaderService = $placeLoaderService;
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
     * Adds additional information to given places.
     *
     * @param string $locationFull
     * @param string|null $location
     * @param string $sortBy
     * @param Place[] $placeResults
     * @param int $page
     * @return void
     * @throws Exception
     */
    protected function addAdditionalInformationToPlaces(string $locationFull, ?string $location = null, string $sortBy = self::ORDER_BY_RELEVANCE, array &$placeResults = [], int $page = 1): void
    {
        /* No places given. */
        if (count($placeResults) <= 0) {
            return;
        }

        /* Add distance. */
        if ($location !== null) {
            $locationSplit = preg_split('~,~', $location);

            if ($locationSplit === false) {
                throw new Exception(sprintf('Unable to split string (%s:%d).', __FILE__, __LINE__));
            }

            list($latitude, $longitude) = $locationSplit;

            foreach ($placeResults as $placeResult) {
                $distanceMeter = LocationDataService::getDistanceBetweenTwoPointsInMeter(
                    floatval($latitude),
                    floatval($longitude),
                    $placeResult->getLatitude(),
                    $placeResult->getLongitude()
                );

                $placeResult->setDistanceMeter($distanceMeter);
            }
        }

        /* Add relevance. */
        foreach ($placeResults as $placeSource) {
            $relevance = LocationDataService::getRelevance($locationFull, $sortBy, $placeSource);
            $placeSource->setRelevance($relevance);
        }

        /* Sort by given $sort. */
        switch ($sortBy) {

            /* Sort by distance */
            case self::ORDER_BY_LOCATION:
                usort($placeResults, function (Place $a, Place $b) {
                    return $a->getDistanceMeter() > $b->getDistanceMeter() ? 1 : -1;
                });
                break;

            /* Sort by name */
            case self::ORDER_BY_NAME:
                usort($placeResults, function (Place $a, Place $b) {
                    return $a->getName() > $b->getName() ? 1 : -1;
                });
                break;

            /* Sort by relevance */
            case self::ORDER_BY_RELEVANCE:
            case self::ORDER_BY_RELEVANCE_LOCATION:
                usort($placeResults, function (Place $a, Place $b) {
                    return $a->getRelevance() > $b->getRelevance() ? -1 : 1;
                });
                break;
        }

        $placeResults = array_slice($placeResults, ($page - 1) * self::NUMBER_PER_PAGE, self::NUMBER_PER_PAGE);

        /* Add administration information */
        foreach ($placeResults as $placeResult) {
            $this->placeLoaderService->addAdministrationInformationToPlace($placeResult);
        }
    }

    /**
     * Get position from string submit.
     *
     * @param string $search
     * @param string|null $currentLocation
     * @param string $sortBy
     * @param Place|null $placeMatch
     * @param Place[] $placeResults
     * @param int $numberResults
     * @param int $page
     * @return float[]|null
     * @throws Exception
     */
    protected function getPositionFromStringSubmit(string $search, string $currentLocation = null, string $sortBy = self::ORDER_BY_RELEVANCE, ?Place &$placeMatch = null, array &$placeResults = [], int &$numberResults = 0, int $page = 1): ?array
    {
        $parsed = GPSConverter::parseFullLocation2DecimalDegrees($search);

        if ($parsed !== false) {
            $placeMatch = null;

            return $parsed;
        }

        $placeResults = $this->locationDataService->getLocationsByName($search);

        $numberResults = count($placeResults);

        $this->addAdditionalInformationToPlaces($search, $currentLocation, $sortBy, $placeResults, $page);

        switch (true) {
            case count($placeResults) <= 0:
                throw new InvalidArgumentException(sprintf('Unable to find place "%s".', $search));

            case count($placeResults) == 1:
                $placeMatch = $placeResults[0];
                return [$placeMatch->getLatitude(), $placeMatch->getLongitude()];

            default:
                return null;
        }
    }

    /**
     * Get position from code:id submit.
     *
     * @param string $codeId
     * @param Place|null $placeSource
     * @return float[]
     * @throws Exception
     */
    protected function getPositionFromCodeIdSubmit(string $codeId, ?Place &$placeSource = null): array
    {
        $placeSource = $this->locationDataService->getLocationByCodeId($codeId);

        if ($placeSource === null) {
            throw new InvalidArgumentException(sprintf('Unable to find place with id "%d".', $codeId));
        }

        return [$placeSource->getLatitude(), $placeSource->getLongitude()];
    }

    /**
     * Gets the location data as an array.
     *
     * @param Request $request
     * @param string $search
     * @param string $sortBy
     * @param string|null $currentLocation
     * @param array<string, Place[]> $data
     * @param Place[] $placeResults
     * @param int $numberResults
     * @param int $page
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(Request $request, string &$search, string &$sortBy, ?string &$currentLocation, array &$data = [], array &$placeResults = [], int &$numberResults = 0, int $page = 1): array
    {
        $placeMatch = null;

        /* Move given name query with id content to id query. */
        if ($request->query->has(self::PARAMETER_NAME_QUERY)) {
            $query = trim(strval($request->query->get(self::PARAMETER_NAME_QUERY)));

            if (preg_match('~^[ahlprstuv]:\d+$~', $query)) {
                $request->query->set(self::PARAMETER_NAME_ID, $query);
                $request->query->remove(self::PARAMETER_NAME_QUERY);
            }
        }

        /* Also parameter l (location) given. */
        if ($request->query->has(self::PARAMETER_NAME_LOCATION)) {
            $currentLocation = strval($request->query->get(self::PARAMETER_NAME_LOCATION));
        } else {
            $currentLocation = null;
        }

        /* Also parameter s (sort) given. */
        if ($request->query->has(self::PARAMETER_NAME_SORT)) {
            $sortBy = strval($request->query->get(self::PARAMETER_NAME_SORT));
        }

        switch (true) {
            /* Parameter q given. */
            case $request->query->has(self::PARAMETER_NAME_QUERY):
                $search = strval($request->query->get(self::PARAMETER_NAME_QUERY));

                $position = $this->getPositionFromStringSubmit($search, $currentLocation, $sortBy, $placeMatch, $placeResults, $numberResults, $page);

                if ($position === null) {
                    return [];
                } else {
                    list($latitude, $longitude) = $position;
                    return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeMatch);
                }

            /* Parameter id given (direct place given) */
            // no break
            case $request->query->has(self::PARAMETER_NAME_ID):
                $search = strval($request->query->get(self::PARAMETER_NAME_ID));
                list($latitude, $longitude) = $this->getPositionFromCodeIdSubmit($search, $placeMatch);
                return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeMatch);

            /* No parameter given */
            default:
                return [];
        }
    }

    /**
     * Location route.
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/location', name: BaseController::ROUTE_NAME_APP_LOCATION)]
    public function location(Request $request): Response
    {
        $verbose = $request->query->has(self::PARAMETER_NAME_VERBOSE);
        $places = [];
        $results = [];
        $numberResults = 0;
        $search = '';
        $currentLocation = null;
        $sortBy = 'r'; /* r - relevance, n - name, l - location (needs $location !== 0), rl - relevance and location (needs $location !== 0) */
        $error = null;
        $page = $request->query->has(self::PARAMETER_NAME_PAGE) ? intval($request->query->get(self::PARAMETER_NAME_PAGE)) : 1;
        $numberPerPage = self::NUMBER_PER_PAGE;

        $this->locationDataService->setVerbose($verbose, false);

        try {
            $locationData = $this->getLocationData($request, $search, $sortBy, $currentLocation, $places, $results, $numberResults, $page);
        } catch (InvalidArgumentException|Throwable $exception) {
            $error = $this->translator->trans('general.notAvailable', ['%place%' => $search], 'location');

            if ($this->kernel->getEnvironment() === 'dev') {
                $error = sprintf('%s (%s:%d - %s)', $error, __FILE__, __LINE__, $exception->getMessage());
            }

            $locationData = [];
        }

        $nextPage = null;
        $numberLastElement = min($page * $numberPerPage, $numberResults);

        if ($numberResults > $numberLastElement) {
            $nextPage = $page + 1;
        }

        return $this->renderForm('content/location.html.twig', [
            'error' => $error,
            'search' => $search,
            'sort' => $sortBy,
            'currentLocation' => $currentLocation,
            'locationData' => $locationData, /* Show search detail */
            'verbose' => $verbose,
            'places' => $places,
            'results' => $results, /* Show multiple results */

            'page' => $page,
            'numberResults' => $numberResults,
            'numberPerPage' => $numberPerPage,
            'nextPage' => $nextPage,

            'version' => $this->versionService->getVersion(),
        ]);
    }
}
