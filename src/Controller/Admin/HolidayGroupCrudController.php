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
use App\Entity\HolidayGroup;
use App\Service\SecurityService;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HolidayGroupCrudController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-10)
 * @package App\Controller\Admin
 */
class HolidayGroupCrudController extends BaseCrudController
{
    /**
     * HolidayGroupCrudController constructor.
     *
     * @param SecurityService $securityService
     * @param TranslatorInterface $translator
     * @throws Exception
     */
    public function __construct(SecurityService $securityService, TranslatorInterface $translator)
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
        return HolidayGroup::class;
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
}
