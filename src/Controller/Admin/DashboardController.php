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

use App\Entity\Calendar;
use App\Entity\CalendarStyle;
use App\Entity\Event;
use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use App\Entity\Image;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-07)
 * @package App\Controller\Admin
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * Index page of dashboard controller.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws Exception
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        if (!$adminUrlGenerator instanceof AdminUrlGenerator) {
            throw new Exception(sprintf('AdminUrlGenerator class expected (%s:%d).', __FILE__, __LINE__));
        }

        return $this->redirect($adminUrlGenerator->setController(CalendarStyleCrudController::class)->generateUrl());
    }

    /**
     * Configure dashboard.
     *
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PHPCalendar API');
    }

    /**
     * Configure menu items.
     *
     * @return mixed[]
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('admin.dashboard.label', 'fa fa-home');

        yield MenuItem::section('admin.section.calendar.label');
        yield MenuItem::linkToCrud('admin.section.calendar.label', 'fas fa-list', Calendar::class);
        yield MenuItem::linkToCrud('admin.section.event.label', 'fas fa-list', Event::class);

        yield MenuItem::section('admin.section.administration.label');
        yield MenuItem::linkToCrud('admin.section.calendarStyle.label', 'fas fa-list', CalendarStyle::class);
        yield MenuItem::linkToCrud('admin.section.holiday.label', 'fas fa-list', Holiday::class);
        yield MenuItem::linkToCrud('admin.section.holidayGroup.label', 'fas fa-list', HolidayGroup::class);
        yield MenuItem::linkToCrud('admin.section.user.label', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('admin.section.image.label', 'fas fa-list', Image::class);
    }
}
