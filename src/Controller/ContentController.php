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
use App\Utils\GPSConverter;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * ContentController constructor.
     *
     * @param LocationDataService $locationDataService
     * @param TranslatorInterface $translator
     */
    public function __construct(LocationDataService $locationDataService, TranslatorInterface $translator)
    {
        $this->locationDataService = $locationDataService;

        $this->translator = $translator;
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
     * @param Request $request
     * @param FormInterface $form
     * @param array<string, Place[]> $data
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(Request $request, FormInterface $form, array &$data = []): array
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            return [];
        }

        /** @var Location $location */
        $location = $form->getData();

        list($latitude, $longitude) = GPSConverter::parseFullLocation2DecimalDegrees($location->getLocationFull());

        return $this->locationDataService->getLocationDataFormatted($latitude, $longitude, $data);
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
        // creates a task object and initializes some data for this example
        $location = new Location();
        $location->setLocationFull(sprintf('%s %s', Location::DEFAULT_LATITUDE, Location::DEFAULT_LONGITUDE));

        $form = $this->createForm(FullLocationType::class, $location, [
            'method' => Request::METHOD_POST,
        ]);

        $form->handleRequest($request);
        $data = [];

        $error = null;
        try {
            $locationData = $this->getLocationData($request, $form, $data);
        } catch (Throwable $throwable) {
            $error = $this->translator->trans('general.notAvailable', [], 'location');
            $locationData = [];
        }

        return $this->renderForm('content/location.html.twig', [
            'form' => $form,
            'error' => $error,
            'locationData' => $locationData,
            'data' => $data,
        ]);
    }
}
