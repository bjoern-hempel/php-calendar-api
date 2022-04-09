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

namespace App\Command;

use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use App\Repository\HolidayGroupRepository;
use App\Repository\HolidayRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class CreateHolidayCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-06)
 * @package App\Command
 * @example bin/console app:holiday:create 2022
 */
class CreateHolidayCommand extends Command
{
    protected static $defaultName = 'app:holiday:create';

    private EntityManagerInterface $manager;

    private HttpClientInterface $client;

    protected const API_ENDPOINT = 'https://feiertage-api.de/api/?jahr=%d';

    protected const API_DATE_FIELD = 'datum';

    public const API_DATE_FORMAT = 'Y-m-d';

    public const YEARLY_YEAR = 1970;

    protected const YEARLY_DATES = [
        '01-01', // New Year (Neujahr)
        '01-06', // Biblical Magi (Heilige Drei Könige)
        '03-08', // International Women's Day (Frauentag)
        '05-01', // International Workers' Day (Tag der Arbeit)
        '08-08', // Augsburger Hohes Friedensfest
        '08-15', // Assumption of Mary (Mariä Himmelfahrt)
        '10-03', // German Unity Day (Tag der Deutschen Einheit)
        '10-31', // Reformation Day (Reformationstag)
        '11-01', // All Saints' Day (Allerheiligen)
        '11-02', // All Souls' Day (Allerseelen)
        '12-06', // Saint Nicholas (Nikolaus von Myra)
        '12-08', // Mariä Unbefleckte Empfängnis (Immaculate Conception)
        '12-25', // December 25 (25. Dezember)
        '12-26', // December 26 (26. Dezember)
    ];

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $manager
     * @param HttpClientInterface $client
     */
    public function __construct(EntityManagerInterface $manager, HttpClientInterface $client)
    {
        parent::__construct();

        $this->manager = $manager;

        $this->client = $client;
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this
            ->setName(strval(self::$defaultName))
            ->setDescription('Creates holidays from given year.')
            ->setDefinition([
                new InputArgument('year', InputArgument::REQUIRED, 'The year'),
            ])
            ->setHelp(
                <<<'EOT'

The <info>app:holiday:create</info> command creates holidays from given year.

EOT
            );
    }

    /**
     * Gets yearly or normal holiday date including yearly flag.
     *
     * @param string[] $holidayArray
     * @return string[]|bool[]
     */
    protected function getDate(array $holidayArray): array
    {
        $dateString = $holidayArray[self::API_DATE_FIELD];

        $dateStringWithoutYear = preg_replace('~^[0-9]{4,4}-~', '', $dateString);

        $yearly = in_array($dateStringWithoutYear, self::YEARLY_DATES);

        if (!$yearly) {
            return [
                'date' => $dateString,
                'yearly' => false,
            ];
        }

        return [
            'date' => sprintf('%d-%s', self::YEARLY_YEAR, $dateStringWithoutYear),
            'yearly' => true,
        ];
    }

    /**
     * Execute the commands.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws DecodingExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* Read parameter. */
        $year = intval($input->getArgument('year'));

        /* Endpoint */
        $apiEndpoint = sprintf(self::API_ENDPOINT, $year);

        /** @var HolidayGroupRepository $holidayGroupRepository */
        $holidayGroupRepository = $this->manager->getRepository(HolidayGroup::class);

        /** @var HolidayRepository $holidayRepository */
        $holidayRepository = $this->manager->getRepository(Holiday::class);

        $response = $this->client->request(
            'GET',
            $apiEndpoint
        );

        $content = $response->toArray();

        foreach ($content as $shortName => $holidaysArray) {
            $holidayGroup = $holidayGroupRepository->findOneByShortName($shortName);

            if ($holidayGroup === null) {
                continue;
            }

            foreach ($holidaysArray as $name => $holidayArray) {
                if (!array_key_exists(self::API_DATE_FIELD, $holidayArray)) {
                    continue;
                }

                /* Get date. */
                $dateArray = $this->getDate($holidayArray);
                $date = DateTime::createFromFormat(self::API_DATE_FORMAT, strval($dateArray['date']));
                $yearly = boolval($dateArray['yearly']);

                if ($date === false) {
                    throw new Exception(sprintf('Unable to parse date (%s:%d).', __FILE__, __LINE__));
                }

                $holidays = $holidayRepository->findHolidaysByHolidayGroupAndDate($holidayGroup, $date);

                $holidaysCount = count($holidays);

                if ($holidaysCount > 1) {
                    throw new Exception(sprintf('Unexpected number of holidays: %d (%s:%d).', $holidaysCount, __FILE__, __LINE__));
                }

                if ($holidaysCount === 1) {
                    $holidayNew = $holidays[0];
                } else {
                    $holidayNew = new Holiday();
                }

                $holidayNew->setName($name);
                $holidayNew->setDate($date);
                $holidayNew->setType(0);
                $holidayNew->setYearly($yearly);
                $holidayNew->setHolidayGroup($holidayGroup);

                $this->manager->persist($holidayNew);
            }
        }

        $this->manager->flush();

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
