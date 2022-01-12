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

namespace App\DataFixtures;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\CalendarStyle;
use App\Entity\Event;
use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use App\Entity\Image;
use App\Entity\User;
use App\Service\CalendarBuilderService;
use App\Utils\ImageProperty;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
class AppFixtures extends Fixture implements ContainerAwareInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;

    private ?ObjectManager $manager = null;

    private ContainerInterface $container;

    private ImageProperty $imageProperty;

    private const ENVIRONMENT_NAME_DEV = 'dev';

    private const ENVIRONMENT_NAME_TEST = 'test';

    /** @var string[][]|int[][] $calendars  */
    protected array $calendars = [
        /* Titel page */
        0 => [
            'sourcePath' => 'source/00.jpg',
            'targetPath' => 'target/00.jpg',
            'title' => 'Las Palmas, Gran Canaria, Spanien, 2021',
            'position' => '28°09’42.9"N 15°26’05.1"W',
            'year' => 2022,
            'month' => 0,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.google.de',
        ],

        /* 01 */
        1 => [
            'sourcePath' => 'source/01.jpg',
            'targetPath' => 'target/01.jpg',
            'title' => 'Playa de las Canteras, Gran Canaria, Spanien, 2021',
            'position' => '28°08’53.9"N 15°25’53.0"W',
            'year' => 2022,
            'month' => 1,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 02 */
        2 => [
            'sourcePath' => 'source/02.jpg',
            'targetPath' => 'target/02.jpg',
            'title' => 'Artenara, Gran Canaria, Spanien, 2021',
            'position' => '28°01’03.5"N 15°40’08.4"W',
            'year' => 2022,
            'month' => 2,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 03 */
        3 => [
            'sourcePath' => 'source/03.jpg',
            'targetPath' => 'target/03.jpg',
            'title' => 'Brännö, Göteborg, Schweden, 2020',
            'position' => '57°38’12.3"N 11°46’02.6"E',
            'year' => 2022,
            'month' => 3,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 04 */
        4 => [
            'sourcePath' => 'source/04.jpg',
            'targetPath' => 'target/04.jpg',
            'title' => 'Meersburg, Deutschland, 2021',
            'position' => '47°41’36.6"N 9°16’17.6"E',
            'year' => 2022,
            'month' => 4,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 05 */
        5 => [
            'sourcePath' => 'source/05.jpg',
            'targetPath' => 'target/05.jpg',
            'title' => 'Norra Sjöslingan, Göteborg, Schweden, 2020',
            'position' => '57°41’26.3"N 12°02’10.3"E',
            'year' => 2022,
            'month' => 5,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 06 */
        6 => [
            'sourcePath' => 'source/06.jpg',
            'targetPath' => 'target/06.jpg',
            'title' => 'Bregenz, Bodensee, Österreich, 2021',
            'position' => '47°30’29.4"N 9°45’31.6"E',
            'year' => 2022,
            'month' => 6,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
            'url' => 'https://www.facebook.com',
        ],

        /* 07 */
        7 => [
            'sourcePath' => 'source/07.jpg',
            'targetPath' => 'target/07.jpg',
            'title' => 'Badi Triboltingen, Triboltingen, Schweiz, 2021',
            'position' => '47°39’57.2"N 9°06’37.9"E',
            'year' => 2022,
            'month' => 7,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 08 */
        8 => [
            'sourcePath' => 'source/08.jpg',
            'targetPath' => 'target/08.jpg',
            'title' => 'Zürich, Schweiz, 2021',
            'position' => '47°22’22.9"N 8°32’29.0"E',
            'year' => 2022,
            'month' => 8,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
            'url' => 'https://www.facebook.com',
        ],

        /* 09 */
        9 => [
            'sourcePath' => 'source/09.jpg',
            'targetPath' => 'target/09.jpg',
            'title' => 'Stein am Rhein, Schweiz, 2021',
            'position' => '47°39’37.2"N 8°51’30.6"E',
            'year' => 2022,
            'month' => 9,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 10 */
        10 => [
            'sourcePath' => 'source/10.jpg',
            'targetPath' => 'target/10.jpg',
            'title' => 'Insel Mainau, Bodensee, Deutschland, 2021',
            'position' => '47°42’17.5"N 9°11’37.7"E',
            'year' => 2022,
            'month' => 10,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
            'url' => 'https://www.facebook.com',
        ],

        /* 11 */
        11 => [
            'sourcePath' => 'source/11.jpg',
            'targetPath' => 'target/11.jpg',
            'title' => 'Casa Milà, Barcelona, Spanien, 2020',
            'position' => '41°23’43.2"N 2°09’42.4"E',
            'year' => 2022,
            'month' => 11,
            'valign' => CalendarBuilderService::VALIGN_TOP,
            'url' => 'https://www.facebook.com',
        ],

        /* 12 */
        12 => [
            'sourcePath' => 'source/12.jpg',
            'targetPath' => 'target/12.jpg',
            'title' => 'Meersburg, Deutschland, 2021',
            'position' => '47°41’39.0"N 9°16’15.2"E',
            'year' => 2022,
            'month' => 12,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
            'url' => 'https://www.facebook.com',
        ],
    ];

    /** @var string[][] $holidayDatas  */
    protected array $holidayDatas = [
        ['Neujahr', '2022-01-01T12:00:00Z'],
        ['Karfreitag', '2022-04-15T12:00:00Z'],
        ['Ostern', '2022-04-18T12:00:00Z'],
        ['1. Mai', '2022-05-01T12:00:00Z'],
        ['Christi Himmelfahrt', '2022-05-26T12:00:00Z'],
        ['Pfingsten ', '2022-06-06T12:00:00Z'],
        ['Tag der Deutschen Einheit', '2022-10-03T12:00:00Z'],
        ['Reformationstag', '2022-10-31T12:00:00Z'],
        ['Buß- und Bettag', '2022-11-16T12:00:00Z'],
        ['1. Weihnachtsfeiertag', '2022-12-25T12:00:00Z'],
        ['2. Weihnachtsfeiertag', '2022-12-26T12:00:00Z'],
    ];

    /** @var string[][]|int[][] $eventDatas  */
    protected array $eventDatas = [
        ['Angela Merkel', '1954-07-17T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Arnold Schwarzenegger', '1947-07-30T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Bernhard', '2100-12-25T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Björn', '1980-02-02T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Carolin Kebekus', '1980-05-09T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Daniel Radcliffe', '1989-07-23T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Erik', '1970-09-11T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Isabel', '1994-08-18T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Heike', '1970-05-06T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Manuel Neuer', '1986-03-27T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Olaf Scholz', '1958-06-14T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Otto Waalkes', '1948-07-22T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Rico', '2100-08-18T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Sebastian', '1997-05-22T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Sido', '1980-11-30T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['Elisabeth II.', '1926-04-21T12:00:00Z', CalendarBuilderService::EVENT_TYPE_BIRTHDAY],
        ['New York City Marathon', '2022-11-06T12:00:00Z', CalendarBuilderService::EVENT_TYPE_EVENT],
        ['Zrce Spring Break, Croatia', '2022-06-03T12:00:00Z', CalendarBuilderService::EVENT_TYPE_EVENT_GROUP],
    ];

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param ImageProperty $imageProperty
     * @param ObjectManager|null $manager
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, ImageProperty $imageProperty, ObjectManager $manager = null)
    {
        $this->userPasswordHasher = $userPasswordHasher;

        $this->imageProperty = $imageProperty;

        if ($manager !== null) {
            $this->setManager($manager);
        }
    }

    /**
     * Set ObjectManager.
     *
     * @param ObjectManager $manager
     */
    public function setManager(ObjectManager $manager): void
    {
        $this->manager = $manager;
    }

    /**
     * Sets a Holiday resource.
     *
     * @param HolidayGroup $holidayGroup
     * @param string $name
     * @param string $date
     * @return Holiday
     * @throws Exception
     */
    protected function setHoliday(HolidayGroup $holidayGroup, string $name, string $date): Holiday
    {
        $holiday = new Holiday();
        $holiday->setHolidayGroup($holidayGroup);
        $holiday->setName($name);
        $holiday->setDate(new DateTime($date));
        $holiday->setColor('255,255,255,100');
        $this->manager?->persist($holiday);

        return $holiday;
    }

    /**
     * Returns a HolidayGroup resource with its Holiday events.
     *
     * @return HolidayGroup
     * @throws Exception
     */
    public function getHolidayGroup(): HolidayGroup
    {
        /* Get persisted public holiday group */
        $holidayGroup = $this->setHolidayGroup();

        /* Add holidays */
        foreach ($this->holidayDatas as $holidayData) {
            $this->setHoliday($holidayGroup, $holidayData[0], $holidayData[1]);
        }

        return $holidayGroup;
    }

    /**
     * Sets a HolidayGroup resource.
     *
     * @return HolidayGroup
     * @throws Exception
     */
    protected function setHolidayGroup(): HolidayGroup
    {
        $holidayGroup = new HolidayGroup();
        $holidayGroup->setName('Saxony');
        $this->manager?->persist($holidayGroup);

        return $holidayGroup;
    }

    /**
     * Returns a CalendarStyle resource.
     *
     * @return CalendarStyle
     */
    public function getCalendarStyle(): CalendarStyle
    {
        return $this->setCalendarStyle();
    }

    /**
     * Sets a CalendarStyle resource.
     *
     * @return CalendarStyle
     */
    protected function setCalendarStyle(): CalendarStyle
    {
        $calendarStyle = new CalendarStyle();
        $calendarStyle->setName('default');
        $calendarStyle->setConfig([
            'name' => 'default',
        ]);
        $this->manager?->persist($calendarStyle);

        return $calendarStyle;
    }

    /**
     * Returns a User resource.
     *
     * @param CalendarStyle $calendarStyle
     * @param HolidayGroup $holidayGroup
     * @param int|null $i
     * @return User
     * @throws Exception
     */
    public function getUser(CalendarStyle $calendarStyle, HolidayGroup $holidayGroup, ?int $i = 1): User
    {
        $user = $this->setUser($i);

        /* Add events to user */
        foreach ($this->eventDatas as $eventData) {
            $this->setEvent($user, strval($eventData[0]), intval($eventData[2]), strval($eventData[1]));
        }

        /* Create calendar for user */
        $calendar = $this->setCalendar($user, $calendarStyle, $holidayGroup);

        foreach ($this->calendars as $calendarData) {
            /* Create image */
            $image = $this->setImage($user, strval($calendarData['sourcePath']));

            /* Connect calendar with image */
            $this->setCalendarImage(
                $user,
                $calendar,
                $image,
                intval($calendarData['year']),
                intval($calendarData['month']),
                strval($calendarData['title']),
                strval($calendarData['position']),
                intval($calendarData['valign']),
                strval($calendarData['url'])
            );
        }

        return $user;
    }

    /**
     * Sets a User resource.
     *
     * @param int|null $i
     * @return User
     */
    protected function setUser(?int $i = 1): User
    {
        $user = new User();
        $user->setEmail(sprintf('user%d@domain.tld', $i));
        $user->setUsername(sprintf('user%d', $i));
        $user->setPassword($this->userPasswordHasher->hashPassword($user, sprintf('password%d', $i)));
        $user->setFirstname(sprintf('Firstname %d', $i));
        $user->setLastname(sprintf('Lastname %d', $i));
        $user->setIdHash('cf6b37d2b5f805a0f76ef2b3610eff7a705a2290');
        $this->manager?->persist($user);

        return $user;
    }

    /**
     * Sets a Event resource.
     *
     * @param User $user
     * @param string $name
     * @param int $type
     * @param string $date
     * @return Event
     * @throws Exception
     */
    protected function setEvent(User $user, string $name, int $type, string $date): Event
    {
        $event = new Event();
        $event->setUser($user);
        $event->setName($name);
        $event->setType($type);
        $event->setDate(new DateTime($date));
        $event->setColor('255,255,255,100');
        $this->manager?->persist($event);

        return $event;
    }

    /**
     * Sets a Calendar resource.
     *
     * @param User $user
     * @param CalendarStyle $calendarStyle
     * @param HolidayGroup $holidayGroup
     * @return Calendar
     * @throws Exception
     */
    protected function setCalendar(User $user, CalendarStyle $calendarStyle, HolidayGroup $holidayGroup): Calendar
    {
        $calendar = new Calendar();
        $calendar->setUser($user);
        $calendar->setCalendarStyle($calendarStyle);
        $calendar->setHolidayGroup($holidayGroup);
        $calendar->setName(sprintf('Calendar %d', 1));
        $calendar->setTitle('2022');
        $calendar->setSubtitle('With love - Isa & Björn');
        $calendar->setBackgroundColor('255,255,255,100');
        $calendar->setPrintCalendarWeek(true);
        $calendar->setPrintWeekNumber(true);
        $calendar->setPrintQrCodeMonth(true);
        $calendar->setPrintQrCodeTitle(true);
        $calendar->setConfig([
            'background-color' => '255,255,255,100',
            'print-calendar-week' => true,
            'print-week-number' => true,
            'print-qr-code-month' => true,
            'print-qr-code-title' => true,
            'aspect-ratio' => round(sqrt(2), 3), /* 1:1.414 */
            'height' => $this->getEnvironment() === self::ENVIRONMENT_NAME_TEST ? 800 : 4000,
        ]);
        $this->manager?->persist($calendar);

        return $calendar;
    }

    /**
     * Sets an Image resource.
     *
     * @param User $user
     * @param string $sourcePath
     * @return Image
     * @throws Exception
     */
    protected function setImage(User $user, string $sourcePath): Image
    {
        $image = new Image();
        $image->setUser($user);
        $image->setPath($sourcePath);
        $this->imageProperty->init($user, $image, $this->getEnvironment() === self::ENVIRONMENT_NAME_TEST);
        $this->manager?->persist($image);

        return $image;
    }

    /**
     * Return a CalendarImage resource.
     *
     * @param User $user
     * @param Calendar $calendar
     * @param Image $image
     * @param int $year
     * @param int $month
     * @param string $title
     * @param string $position
     * @param int $valign
     * @param string $url
     * @return CalendarImage
     * @throws Exception
     */
    protected function setCalendarImage(User $user, Calendar $calendar, Image $image, int $year, int $month, string $title, string $position, int $valign, string $url): CalendarImage
    {
        $calendarImage = new CalendarImage();
        $calendarImage->setUser($user);
        $calendarImage->setCalendar($calendar);
        $calendarImage->setImage($image);
        $calendarImage->setYear($year);
        $calendarImage->setMonth($month);
        $calendarImage->setTitle($title);
        $calendarImage->setPosition($position);
        $calendarImage->setValign($valign);
        $calendarImage->setUrl($url);
        $calendarImage->setConfig([
            'valign' => $valign,
        ]);
        $this->manager?->persist($calendarImage);

        return $calendarImage;
    }

    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        /* Check environment (only dev or test allowed). */
        if (!in_array($this->getEnvironment(), array(self::ENVIRONMENT_NAME_DEV, self::ENVIRONMENT_NAME_TEST))) {
            throw new Exception(sprintf('Illegal environment "%s" (%s:%d).', $this->getEnvironment(), __FILE__, __LINE__));
        }

        /* Set ObjectManager */
        $this->setManager($manager);

        /* Get and create HolidayGroup resource. */
        $holidayGroup = $this->getHolidayGroup();

        /* Get and create CalendarStyle resource. */
        $calendarStyle = $this->getCalendarStyle();

        /* Create User resources. */
        for ($i = 1; $i <= 1; $i++) {
            $this->getUser($calendarStyle, $holidayGroup, $i);
        }

        /* Save all resources to db. */
        $manager->flush();
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container
     * @return void
     * @throws Exception
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        /* Check container */
        if ($container === null) {
            throw new Exception(sprintf('Container is missing (%s:%d).', __FILE__, __LINE__));
        }

        $this->container = $container;
    }

    /**
     * Returns the container.
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Returns the kernel.
     *
     * @return KernelInterface
     * @throws Exception
     */
    public function getKernel(): KernelInterface
    {
        $kernel = $this->container->get('kernel');

        if (!$kernel instanceof KernelInterface) {
            throw new Exception(sprintf('Kernel class expected (%s:%d)', __FILE__, __LINE__));
        }

        return $kernel;
    }

    /**
     * Gets the environment.
     *
     * @return string
     * @throws Exception
     */
    public function getEnvironment(): string
    {
        return $this->getKernel()->getEnvironment();
    }
}
