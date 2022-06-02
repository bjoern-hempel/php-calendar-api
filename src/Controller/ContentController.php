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
     * Gets the location data.
     *
     * @param FormInterface $form
     * @param Request $request
     * @param array<string, Place[]> $data
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(FormInterface $form, Request $request, array &$data = []): array
    {
        $locationFull = null;

        if ($locationFull === null && $form->isSubmitted() && $form->isValid()) {
            /** @var Location $location */
            $location = $form->getData();

            $locationFull = $location->getLocationFull();
        }

        if ($locationFull === null && !empty(strval($request->query->get(self::PARAMETER_NAME_QUERY)))) {
            /** @var Location $location */
            $location = $form->getData();

            $locationFull = $location->getLocationFull();
        }

        /* No query given or found. */
        if ($locationFull === null) {
            return [];
        }

        $parsed = GPSConverter::parseFullLocation2DecimalDegrees($locationFull);

        if ($parsed === false) {
            $placeSource = $this->locationDataService->getLocationByName($locationFull);

            if ($placeSource === null) {
                throw new InvalidArgumentException(sprintf('Unable to find place "%s".', $locationFull));
            }

            $latitude = $placeSource->getCoordinate()->getLongitude();
            $longitude = $placeSource->getCoordinate()->getLatitude();
        } else {
            $placeSource = null;
            list($latitude, $longitude) = $parsed;
        }

        return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data, $placeSource);
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
        $locationFull = strval($request->query->get(self::PARAMETER_NAME_QUERY));

        // creates a task object and initializes some data for this example
        $location = new Location();
        $location->setLocationFull($locationFull);

        $form = $this->createForm(FullLocationType::class, $location, [
            'method' => Request::METHOD_POST,
        ]);

        $form->handleRequest($request);
        $data = [];

        $error = null;
        try {
            $locationData = $this->getLocationData($form, $request, $data);
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
            'locationData' => $locationData,
            'data' => $data,
            'version' => $this->versionService->getVersion(),
        ]);
    }
}
