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
use App\Form\Type\FullLocationType;
use App\Service\LocationDataService;
use App\Utils\GPSConverter;
use Exception;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * ContentController constructor.
     *
     * @param LocationDataService $locationDataService
     */
    public function __construct(LocationDataService $locationDataService)
    {
        $this->locationDataService = $locationDataService;
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
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getLocationData(Request $request, FormInterface $form): array
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            return [];
        }

        /** @var Location $location */
        $location = $form->getData();

        list($latitude, $longitude) = GPSConverter::parseFullLocation2DecimalDegrees($location->getLocationFull());

        return $this->locationDataService->getLocationDataFormatted($latitude, $longitude);
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
        $location->setLocationFull('47.900635,13.601868');

        $form = $this->createForm(FullLocationType::class, $location, [
            'method' => Request::METHOD_POST,
        ]);

        $form->handleRequest($request);

        $locationData = $this->getLocationData($request, $form);

        return $this->renderForm('content/location.html.twig', [
            'form' => $form,
            'locationData' => $locationData,
        ]);
    }
}
