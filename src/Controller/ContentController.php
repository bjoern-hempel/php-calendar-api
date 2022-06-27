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

    public const PARAMETER_NAME_QUERY = 'q';

    public const PARAMETER_NAME_LOCATION = 'l';

    public const PARAMETER_NAME_SORT = 's';

    public const PARAMETER_NAME_ID = 'id';

    public const ORDER_BY_LOCATION = 'l';

    public const ORDER_BY_NAME = 'n';

    public const ORDER_BY_RELEVANCE = 'r';

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
     * @param Place[] $placesSource
     * @param string $locationFull
     * @param string|null $location
     * @param string $sort
     * @return void
     * @throws Exception
     */
    protected function addAdditionalInformationToPlaces(array &$placesSource, string $locationFull, ?string $location = null, string $sort = self::ORDER_BY_RELEVANCE): void
    {
        if (count($placesSource) <= 0) {
            return;
        }

        if ($location !== null) {
            $locationSplit = preg_split('~,~', $location);

            if ($locationSplit === false) {
                throw new Exception(sprintf('Unable to split string (%s:%d).', __FILE__, __LINE__));
            }

            list($latitude, $longitude) = $locationSplit;

            foreach ($placesSource as $placeSource) {
                $distanceMeter = LocationDataService::getDistanceBetweenTwoPointsInMeter(
                    floatval($latitude),
                    floatval($longitude),
                    $placeSource->getLatitude(),
                    $placeSource->getLongitude()
                );

                $placeSource->setDistanceMeter($distanceMeter);
            }
        }

        foreach ($placesSource as $placeSource) {
            $relevance = LocationDataService::getRelevance($locationFull, $placeSource);
            $placeSource->setRelevance($relevance);
        }

        /* Sort by given $sort. */
        switch ($sort) {

            /* Sort by distance */
            case self::ORDER_BY_LOCATION:
                usort($placesSource, function (Place $a, Place $b) {
                    return $a->getDistanceMeter() > $b->getDistanceMeter() ? 1 : -1;
                });
                break;

            /* Sort by name */
            case self::ORDER_BY_NAME:
                usort($placesSource, function (Place $a, Place $b) {
                    return $a->getName() > $b->getName() ? 1 : -1;
                });
                break;

            /* Sort by relevance */
            case self::ORDER_BY_RELEVANCE:
                usort($placesSource, function (Place $a, Place $b) {
                    return $a->getRelevance() > $b->getRelevance() ? -1 : 1;
                });
                break;
        }

        /* Add administration information */
        foreach ($placesSource as $placeSource) {
            $this->placeLoaderService->addAdministrationInformationToPlace($placeSource);
        }
    }

    /**
     * Get position from string submit.
     *
     * @param string $locationFull
     * @param Place|null $placeSource
     * @param Place[] $placesSource
     * @param string|null $location
     * @param string $sort
     * @return float[]|null
     * @throws Exception
     */
    protected function getPositionFromStringSubmit(string $locationFull, ?Place &$placeSource = null, array &$placesSource = [], string $location = null, string $sort = self::ORDER_BY_RELEVANCE): ?array
    {
        $parsed = GPSConverter::parseFullLocation2DecimalDegrees($locationFull);

        if ($parsed !== false) {
            $placeSource = null;

            return $parsed;
        }

        $placesSource = $this->locationDataService->getLocationsByName($locationFull);

        $this->addAdditionalInformationToPlaces($placesSource, $locationFull, $location, $sort);

        switch (true) {
            case count($placesSource) <= 0:
                throw new InvalidArgumentException(sprintf('Unable to find place "%s".', $locationFull));

            case count($placesSource) == 1:
                $placeSource = $placesSource[0];
                return [$placeSource->getLatitude(), $placeSource->getLongitude()];

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
     * @param string $sort
     * @param string|null $location
     * @param array<string, Place[]> $data
     * @param Place[] $results
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(Request $request, string &$search, string &$sort, ?string &$location, array &$data = [], array &$results = []): array
    {
        $placeSource = null;

        /* Move given name query with id content to id query. */
        if ($request->query->has(self::PARAMETER_NAME_QUERY)) {
            $query = strval($request->query->get(self::PARAMETER_NAME_QUERY));

            if (preg_match('~^[ahlprstuv]:\d+$~', $query)) {
                $request->query->set(self::PARAMETER_NAME_ID, $query);
                $request->query->remove(self::PARAMETER_NAME_QUERY);
            }
        }

        /* Also parameter l (location) given. */
        if ($request->query->has(self::PARAMETER_NAME_LOCATION)) {
            $location = strval($request->query->get(self::PARAMETER_NAME_LOCATION));
        } else {
            $location = null;
        }

        /* Also parameter s (sort) given. */
        if ($request->query->has(self::PARAMETER_NAME_SORT)) {
            $sort = strval($request->query->get(self::PARAMETER_NAME_SORT));
        }

        switch (true) {
            /* Parameter q given. */
            case $request->query->has(self::PARAMETER_NAME_QUERY):
                $search = strval($request->query->get(self::PARAMETER_NAME_QUERY));

                $position = $this->getPositionFromStringSubmit($search, $placeSource, $results, $location, $sort);

                if ($position === null) {
                    return [];
                } else {
                    list($latitude, $longitude) = $position;
                    return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeSource);
                }

            /* Parameter id given (direct place given) */
            // no break
            case $request->query->has(self::PARAMETER_NAME_ID):
                $search = strval($request->query->get(self::PARAMETER_NAME_ID));
                list($latitude, $longitude) = $this->getPositionFromCodeIdSubmit($search, $placeSource);
                return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeSource);

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
        $places = [];
        $results = [];
        $search = '';
        $location = null;
        $sort = 'r'; /* r - relevance, n - name, l - location (needs $location !== 0) */
        $error = null;

        try {
            $locationData = $this->getLocationData($request, $search, $sort, $location, $places, $results);
        } catch (InvalidArgumentException|Throwable $exception) {
            $error = $this->translator->trans('general.notAvailable', ['%place%' => $search], 'location');

            if ($this->kernel->getEnvironment() === 'dev') {
                $error = sprintf('%s (%s:%d - %s)', $error, __FILE__, __LINE__, $exception->getMessage());
            }

            $locationData = [];
        }

        return $this->renderForm('content/location.html.twig', [
            'error' => $error,
            'search' => $search,
            'sort' => $sort,
            'location' => $location,
            'locationData' => $locationData, /* Show search detail */
            'places' => $places,
            'results' => $results, /* Show multiple results */
            'version' => $this->versionService->getVersion(),
        ]);
    }
}
