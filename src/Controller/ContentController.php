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
use App\Entity\Location;
use App\Entity\Place;
use App\Form\Type\FullLocationType;
use App\Service\LocationDataService;
use App\Service\VersionService;
use App\Utils\GPSConverter;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Form\FormInterface;
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

    public const PARAMETER_NAME_QUERY = 'q';

    public const PARAMETER_NAME_ID = 'id';

    public const SUBMIT_TYPE_FORM = 'form';

    public const SUBMIT_TYPE_QUERY = 'query';

    public const SUBMIT_TYPE_ID = 'id';

    /**
     * ContentController constructor.
     *
     * @param LocationDataService $locationDataService
     * @param TranslatorInterface $translator
     * @param KernelInterface $kernel
     * @param VersionService $versionService
     */
    public function __construct(LocationDataService $locationDataService, TranslatorInterface $translator, KernelInterface $kernel, VersionService $versionService)
    {
        $this->locationDataService = $locationDataService;

        $this->translator = $translator;

        $this->kernel = $kernel;

        $this->versionService = $versionService;
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
     * Returns the submit type.
     *
     * @param FormInterface $form
     * @param Request $request
     * @return string|null
     */
    protected function getSubmitType(FormInterface $form, Request $request): ?string
    {
        return match (true) {
            $form->isSubmitted() && $form->isValid() => self::SUBMIT_TYPE_FORM,
            !empty(strval($request->query->get(self::PARAMETER_NAME_QUERY))) => self::SUBMIT_TYPE_QUERY,
            !empty(strval($request->query->get(self::PARAMETER_NAME_ID))) => self::SUBMIT_TYPE_ID,
            default => null,
        };
    }

    /**
     * Get position from string submit.
     *
     * @param string $locationFull
     * @param Place|null $placeSource
     * @param Place[] $placesSource
     * @return float[]|null
     * @throws Exception
     */
    protected function getPositionFromStringSubmit(string $locationFull, ?Place &$placeSource = null, array &$placesSource = []): ?array
    {
        $parsed = GPSConverter::parseFullLocation2DecimalDegrees($locationFull);

        if ($parsed !== false) {
            $placeSource = null;

            return $parsed;
        }

        $placesSource = $this->locationDataService->getLocationsByName($locationFull);

        switch (true) {
            case count($placesSource) <= 0:
                throw new InvalidArgumentException(sprintf('Unable to find place "%s".', $locationFull));

            case count($placesSource) == 1:
                $placeSource = $placesSource[0];
                return [$placeSource->getCoordinate()->getLongitude(), $placeSource->getCoordinate()->getLatitude()];

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

        return [$placeSource->getCoordinate()->getLongitude(), $placeSource->getCoordinate()->getLatitude()];
    }

    /**
     * Gets the location data as an array.
     *
     * @param FormInterface $form
     * @param Request $request
     * @param array<string, Place[]> $data
     * @param Place[] $results
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(FormInterface $form, Request $request, array &$data = [], array &$results = []): array
    {
        $placeSource = null;

        $formData = $form->getData();

        if (!$formData instanceof Location) {
            throw new Exception(sprintf('Unable to get data (%s:%d).', __FILE__, __LINE__));
        }

        switch ($this->getSubmitType($form, $request)) {
            case self::SUBMIT_TYPE_FORM:
            case self::SUBMIT_TYPE_QUERY:
                $position = $this->getPositionFromStringSubmit($formData->getLocationFull(), $placeSource, $results);

                if ($position === null) {
                    return [];
                } else {
                    list($latitude, $longitude) = $position;
                    return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeSource);
                }

                // no break
            case self::SUBMIT_TYPE_ID:
                list($latitude, $longitude) = $this->getPositionFromCodeIdSubmit($formData->getLocationFull(), $placeSource);
                return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeSource);

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
        $locationFull = match (true) {
            $request->query->has(self::PARAMETER_NAME_QUERY) => strval($request->query->get(self::PARAMETER_NAME_QUERY)),
            $request->query->has(self::PARAMETER_NAME_ID) => strval($request->query->get(self::PARAMETER_NAME_ID)),
            default => '',
        };

        // creates a task object and initializes some data for this example
        $location = new Location();
        $location->setLocationFull($locationFull);

        $form = $this->createForm(FullLocationType::class, $location, [
            'method' => Request::METHOD_POST,
        ]);

        $form->handleRequest($request);
        $data = [];
        $results = [];
        $search = '';

        $error = null;
        try {
            $locationData = $this->getLocationData($form, $request, $data, $results);

            /** @var Location $location */
            $location = $form->getData();

            $search = $location->getLocationFull();
        } catch (InvalidArgumentException $exception) {
            /** @var Location $location */
            $location = $form->getData();

            $error = $this->kernel->getEnvironment() !== 'dev' ?
                $this->translator->trans('general.notAvailable', ['%place%' => $location->getLocationFull()], 'location') :
                sprintf('%s (%s:%d)', $exception->getMessage(), $exception->getFile(), $exception->getLine());
            $locationData = [];
        } catch (Throwable $exception) {
            /** @var Location $location */
            $location = $form->getData();

            $error = $this->kernel->getEnvironment() !== 'dev' ?
                $this->translator->trans('general.notAvailable', ['%place%' => $location->getLocationFull()], 'location') :
                sprintf('%s (%s:%d)', $exception->getMessage(), $exception->getFile(), $exception->getLine());
            $locationData = [];
        }

        return $this->renderForm('content/location.html.twig', [
            'form' => $form,
            'error' => $error,
            'search' => $search,
            'locationData' => $locationData,
            'data' => $data,
            'results' => $results,
            'version' => $this->versionService->getVersion(),
        ]);
    }
}
