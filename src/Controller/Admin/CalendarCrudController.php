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
use App\Entity\Calendar;
use App\Service\SecurityService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class CalendarCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-12)
 * @package App\Controller\Admin
 */
class CalendarCrudController extends BaseCrudController
{
    /**
     * CalendarCrudController constructor.
     *
     * @param SecurityService $securityService
     * @throws Exception
     */
    public function __construct(SecurityService $securityService)
    {
        parent::__construct($securityService);
    }

    /**
     * Return fqcn of this class.
     *
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
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
     * Configures filters.
     *
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('title')
            ->add('subtitle')
            ->add('user');
    }
}
