<?php declare(strict_types=1);

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
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AppFixtures
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-30)
 * @package App\Entity
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    /** @var string[][]|int[][] $calendars  */
    protected array $calendars = [
        /* Titel page */
        0 => [
            'height' => 4000,
            'sourcePath' => 'images/00.jpg',
            'targetPath' => 'images/calendar/00.jpg',
            'title' => 'Las Palmas, Gran Canaria, Spanien, 2021',
            'position' => '28°09’42.9"N 15°26’05.1"W',
            'year' => 2022,
            'month' => 0,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 01 */
        1 => [
            'height' => 4000,
            'sourcePath' => 'images/01.jpg',
            'targetPath' => 'images/calendar/01.jpg',
            'title' => 'Playa de las Canteras, Gran Canaria, Spanien, 2021',
            'position' => '28°08’53.9"N 15°25’53.0"W',
            'year' => 2022,
            'month' => 1,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 02 */
        2 => [
            'height' => 4000,
            'sourcePath' => 'images/02.jpg',
            'targetPath' => 'images/calendar/02.jpg',
            'title' => 'Artenara, Gran Canaria, Spanien, 2021',
            'position' => '28°01’03.5"N 15°40’08.4"W',
            'year' => 2022,
            'month' => 2,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 03 */
        3 => [
            'height' => 4000,
            'sourcePath' => 'images/03.jpg',
            'targetPath' => 'images/calendar/03.jpg',
            'title' => 'Brännö, Göteborg, Schweden, 2020',
            'position' => '57°38’12.3"N 11°46’02.6"E',
            'year' => 2022,
            'month' => 3,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 04 */
        4 => [
            'height' => 4000,
            'sourcePath' => 'images/04.jpg',
            'targetPath' => 'images/calendar/04.jpg',
            'title' => 'Meersburg, Deutschland, 2021',
            'position' => '47°41’36.6"N 9°16’17.6"E',
            'year' => 2022,
            'month' => 4,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 05 */
        5 => [
            'height' => 4000,
            'sourcePath' => 'images/05.jpg',
            'targetPath' => 'images/calendar/05.jpg',
            'title' => 'Norra Sjöslingan, Göteborg, Schweden, 2020',
            'position' => '57°41’26.3"N 12°02’10.3"E',
            'year' => 2022,
            'month' => 5,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 06 */
        6 => [
            'height' => 4000,
            'sourcePath' => 'images/06.jpg',
            'targetPath' => 'images/calendar/06.jpg',
            'title' => 'Bregenz, Bodensee, Österreich, 2021',
            'position' => '47°30’29.4"N 9°45’31.6"E',
            'year' => 2022,
            'month' => 6,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
        ],

        /* 07 */
        7 => [
            'height' => 4000,
            'sourcePath' => 'images/07.jpg',
            'targetPath' => 'images/calendar/07.jpg',
            'title' => 'Badi Triboltingen, Triboltingen, Schweiz, 2021',
            'position' => '47°39’57.2"N 9°06’37.9"E',
            'year' => 2022,
            'month' => 7,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 08 */
        8 => [
            'height' => 4000,
            'sourcePath' => 'images/08.jpg',
            'targetPath' => 'images/calendar/08.jpg',
            'title' => 'Zürich, Schweiz, 2021',
            'position' => '47°22’22.9"N 8°32’29.0"E',
            'year' => 2022,
            'month' => 8,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
        ],

        /* 09 */
        9 => [
            'height' => 4000,
            'sourcePath' => 'images/09.jpg',
            'targetPath' => 'images/calendar/09.jpg',
            'title' => 'Stein am Rhein, Schweiz, 2021',
            'position' => '47°39’37.2"N 8°51’30.6"E',
            'year' => 2022,
            'month' => 9,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 10 */
        10 => [
            'height' => 4000,
            'sourcePath' => 'images/10.jpg',
            'targetPath' => 'images/calendar/10.jpg',
            'title' => 'Insel Mainau, Bodensee, Deutschland, 2021',
            'position' => '47°42’17.5"N 9°11’37.7"E',
            'year' => 2022,
            'month' => 10,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
        ],

        /* 11 */
        11 => [
            'height' => 4000,
            'sourcePath' => 'images/11.jpg',
            'targetPath' => 'images/calendar/11.jpg',
            'title' => 'Casa Milà, Barcelona, Spanien, 2020',
            'position' => '41°23’43.2"N 2°09’42.4"E',
            'year' => 2022,
            'month' => 11,
            'valign' => CalendarBuilderService::VALIGN_TOP,
        ],

        /* 12 */
        12 => [
            'height' => 4000,
            'sourcePath' => 'images/12.jpg',
            'targetPath' => 'images/calendar/12.jpg',
            'title' => 'Meersburg, Deutschland, 2021',
            'position' => '47°41’39.0"N 9°16’15.2"E',
            'year' => 2022,
            'month' => 12,
            'valign' => CalendarBuilderService::VALIGN_BOTTOM,
        ],
    ];

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $year = 2022;

        /* Add public holiday group */
        $holidayGroup = new HolidayGroup();
        $holidayGroup->setName('Saxony');
        $manager->persist($holidayGroup);

        $holidayDatas = [
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

        foreach ($holidayDatas as $holidayData) {
            $holiday = new Holiday();
            $holiday->setHolidayGroup($holidayGroup);
            $holiday->setName($holidayData[0]);
            $holiday->setDate(new DateTime($holidayData[1]));
            $holiday->setColor('255,255,255,100');
            $manager->persist($holiday);
        }

        /* Create calendar style */
        $calendarStyle = new CalendarStyle();
        $calendarStyle->setName('default');
        $calendarStyle->setConfig([
            'name' => 'default',
        ]);
        $manager->persist($calendarStyle);

        /* Add user and events */
        for ($i = 1; $i <= 1; $i++) {
            $user = new User();
            $user->setEmail(sprintf('user%d@domain.tld', $i));
            $user->setUsername(sprintf('user%d', $i));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, sprintf('password%d', $i)));
            $user->setFirstname(sprintf('Firstname %d', $i));
            $user->setLastname(sprintf('Lastname %d', $i));
            $manager->persist($user);

            /* Add events to user */
            for ($j = 1; $j <= 5; $j++) {
                $event = new Event();
                $event->setUser($user);
                $event->setName(sprintf('Event %d', $j));
                $event->setDate(new DateTime(sprintf('2022-%02d-02T12:00:00Z', $j)));
                $event->setColor('255,255,255,100');
                $manager->persist($event);
            }

            /* Create calendar for user */
            $calendar = new Calendar();
            $calendar->setUser($user);
            $calendar->setCalendarStyle($calendarStyle);
            $calendar->setHolidayGroup($holidayGroup);
            $calendar->setName(sprintf('Calendar %d', 1));
            $calendar->setTitle('2022');
            $calendar->setSubtitle('With love'.' - '.'Isa & Björn');
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
            ]);
            $manager->persist($calendar);

            foreach ($this->calendars as $calendarData) {
                /* Create image */
                $image = new Image();
                $image->setUser($user);
                $image->setPath(strval($calendarData['sourcePath']));
                $image->setWidth(6000);
                $image->setHeight(4000);
                $image->setSize(0);
                $manager->persist($image);

                /* Connect calendar with image */
                $calendarImage = new CalendarImage();
                $calendarImage->setUser($user);
                $calendarImage->setCalendar($calendar);
                $calendarImage->setImage($image);
                $calendarImage->setYear(intval($calendarData['year']));
                $calendarImage->setMonth(intval($calendarData['month']));
                $calendarImage->setTitle(strval($calendarData['title']));
                $calendarImage->setPosition(strval($calendarData['position']));
                $calendarImage->setValign(intval($calendarData['valign']));
                $calendarImage->setConfig([
                    'valign' => intval($calendarData['valign']),
                ]);
                $manager->persist($calendarImage);
            }
        }

        $manager->flush();
    }
}
