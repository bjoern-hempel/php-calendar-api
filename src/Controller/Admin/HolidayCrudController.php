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

namespace App\Controller\Admin;

use App\Controller\Admin\Base\BaseCrudController;
use App\Entity\Holiday;
use App\Service\Entity\HolidayGroupLoaderService;
use App\Service\SecurityService;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HolidayCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-10)
 * @package App\Controller\Admin
 */
class HolidayCrudController extends BaseCrudController
{
    final public const PARAMETER_HOLIDAY_GROUP = 'holidayGroup';

    /**
     * HolidayCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     * @param HolidayGroupLoaderService $holidayGroupLoaderService
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator, protected RequestStack $requestStack, protected HolidayGroupLoaderService $holidayGroupLoaderService)
    {
        parent::__construct($securityService, $translator);
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Holiday::class;
    }

    /**
     * Returns the entity of this class.
     *
     * @return string
     */
    #[Pure]
    public function getEntity(): string
    {
        return self::getEntityFqcn();
    }

    /**
     * Set default settings from
     *
     * @param string $entityFqcn
     * @return Holiday
     * @throws Exception
     */
    public function createEntity(string $entityFqcn): Holiday
    {
        /** @var Holiday $holiday */
        $holiday = new $entityFqcn();

        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        $query = $currentRequest->query;

        if ($query->has(self::PARAMETER_HOLIDAY_GROUP)) {
            $holidayGroupId = intval($query->get(self::PARAMETER_HOLIDAY_GROUP));

            $holidayGroup = $this->holidayGroupLoaderService->findOneById($holidayGroupId);

            $holiday->setHolidayGroup($holidayGroup);
        }

        return $holiday;
    }
}
