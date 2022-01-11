<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Tests\Functional;

use App\Entity\CalendarImage;
use App\Entity\HolidayGroup;
use App\Service\CalendarBuilderService;
use App\Service\CalendarLoaderService;
use App\Service\HolidayGroupLoaderService;
use App\Tests\Library\DbHelper;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CreatePageTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-08)
 * @package App\Tests\Unit\Utils
 * @see https://symfony.com/doc/current/testing.html
 */
final class CreatePageTest extends KernelTestCase
{
    private CalendarBuilderService $calendarBuilderService;

    private CalendarLoaderService $calendarLoaderService;

    private HolidayGroupLoaderService $holidayGroupLoaderService;

    protected static string $projectDir;

    protected static bool $keepDataBetweenTests = false;

    protected static bool $setUpDone = false;

    protected static bool $clearDB = true;

    /**
     * This method is called before class.
     *
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        /* If setup is already done. Stop here. */
        if (self::$setUpDone) {
            return;
        }

        /* Boot kernel to get AppKernel */
        self::bootKernel();

        /* Setup is already done */
        self::$setUpDone = true;

        /* Get project dir */
        self::$projectDir = self::$kernel->getProjectDir();

        if (self::$keepDataBetweenTests || !self::$clearDB) {
            return;
        }

        /* Build the db helper */
        $dbHelper = new DbHelper(self::$kernel);

        /* Empty test table */
        $dbHelper->printAndExecuteCommands([
            '/* Drop schema */' => 'doctrine:schema:drop --force --env=%(environment)s',
            '/* Create schema */' => 'doctrine:schema:create --env=%(environment)s',
            '/* Load fixtures */' => 'doctrine:fixtures:load -n --env=%(environment)s', # --group=test',
        ]);
    }

    /**
     * Load service from dependency injection.
     *
     * @template T of object
     * @param class-string<T> $className
     * @param int $invalidBehavior
     * @return T
     * @throws Exception
     */
    protected function getService(string $className, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): object
    {
        $service = self::$kernel->getContainer()->get($className, $invalidBehavior);

        /* Check service */
        if ($service === null) {
            throw new Exception(sprintf('Unable to get doctrine (%s:%d)', __FILE__, __LINE__));
        }

        if ($service instanceof $className) {
            return $service;
        }

        throw new Exception(sprintf('Wrong class (%s:%d)', __FILE__, __LINE__));
    }

    /**
     * Gets doctrine registry.
     *
     * @return Registry
     * @throws Exception
     */
    protected function getDoctrine(): Registry
    {
        /** @var ?Registry $doctrine */
        $doctrine = self::$kernel->getContainer()->get('doctrine');

        /* Check registry */
        if ($doctrine === null) {
            throw new Exception(sprintf('Unable to get doctrine (%s:%d)', __FILE__, __LINE__));
        }

        return $doctrine;
    }

    /**
     * This method is called before each test.
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->calendarLoaderService = $this->getService(CalendarLoaderService::class);

        $this->holidayGroupLoaderService = $this->getService(HolidayGroupLoaderService::class);

        $this->calendarBuilderService = $this->getService(CalendarBuilderService::class);
    }

    /**
     * Get HolidayGroup and CalendarImage resource from year and month.
     *
     * Todo: load fixtures into an in-memory database
     * - https://stackoverflow.com/questions/19368274/how-to-create-tests-w-doctrine-entities-without-persisting-them-how-to-set-id/41099633
     * - https://arueckauer.github.io/posts/2020/06/how-to-write-unit-tests-with-phpunit-involving-doctrine-entities/
     *
     * @param string $email
     * @param string $calendarName
     * @param int $year
     * @param int $month
     * @return CalendarImage
     * @throws NonUniqueResultException
     */
    protected function getCalendarImage(string $email, string $calendarName, int $year, int $month): CalendarImage
    {
        return $this->calendarLoaderService->loadCalendarImage($email, $calendarName, $year, $month);
    }

    /**
     * Get HolidayGroup from holiday group name.
     *
     * @param string $holidayGroupName
     * @return HolidayGroup
     * @throws Exception
     */
    protected function getHolidayGroup(string $holidayGroupName): HolidayGroup
    {
        return $this->holidayGroupLoaderService->loadHolidayGroup($holidayGroupName);
    }

    /**
     * Create Page
     *
     * @test
     * @throws Exception
     */
    public function main(): void
    {
        /* Parameter */
        $email = 'user1@domain.tld';
        $calendarName = 'Calendar 1';
        $year = 2022;
        $month = 1;
        $holidayGroupName = 'Saxony';

        /* Arrange */
        $calendarImage = $this->getCalendarImage($email, $calendarName, $year, $month);
        $holidayGroup = $this->getHolidayGroup($holidayGroupName);
        $aspectRatio = floatval($calendarImage->getCalendar()->getConfigObject()->get('aspect-ratio'));
        $height = intval($calendarImage->getCalendar()->getConfigObject()->get('height'));
        $width = intval(floor($height * $aspectRatio));

        /* Act */
        $this->calendarBuilderService->init($calendarImage, $holidayGroup);
        $file = $this->calendarBuilderService->build();

        /* Assert */
        $this->assertSame($file['widthTarget'], $width);
        $this->assertSame($file['heightTarget'], $height);
    }
}
