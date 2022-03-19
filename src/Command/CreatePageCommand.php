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

use App\Entity\CalendarImage;
use App\Service\CalendarBuilderService;
use App\Service\Entity\CalendarLoaderService;
use App\Service\Entity\HolidayGroupLoaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CreatePageCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-29)
 * @package App\Command
 * @example bin/console calendar:create-page --email "user1@domain.tld" --name "Calendar 1" --year 2022 --month 0
 */
class CreatePageCommand extends Command
{
    protected static $defaultName = 'calendar:create-page';

    protected KernelInterface $appKernel;

    protected CalendarLoaderService $calendarLoaderService;

    protected HolidayGroupLoaderService $holidayGroupLoaderService;

    protected EntityManagerInterface $manager;

    /**
     * CreatePageCommand constructor
     *
     * @param KernelInterface $appKernel
     * @param CalendarLoaderService $calendarLoaderService
     * @param HolidayGroupLoaderService $holidayGroupLoaderService
     * @param EntityManagerInterface $manager
     */
    public function __construct(KernelInterface $appKernel, CalendarLoaderService $calendarLoaderService, HolidayGroupLoaderService $holidayGroupLoaderService, EntityManagerInterface $manager)
    {
        $this->appKernel = $appKernel;

        $this->calendarLoaderService = $calendarLoaderService;

        $this->holidayGroupLoaderService = $holidayGroupLoaderService;

        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        /* Set command name */
        if (CreatePageCommand::$defaultName !== null) {
            $this->setName(CreatePageCommand::$defaultName);
        }

        $this
            ->setDescription('Creates a calendar page')
            ->setDefinition([
                new InputOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the user.'),
                new InputOption('name', null, InputOption::VALUE_OPTIONAL, 'The calendar name which will be used.'),
                new InputOption('id', null, InputOption::VALUE_OPTIONAL, 'The calendar id which will be used.'),
                new InputOption('year', 'y', InputOption::VALUE_REQUIRED, 'The year with which the page will be created.'),
                new InputOption('month', 'm', InputOption::VALUE_REQUIRED, 'The month with which the page will be created.'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>calendar:create-page</info> creates a calendar page:
  <info>php %command.full_name%</info>
Creates a calendar page.
EOT
            );
    }

    /**
     * Execute the commands.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '',
            '============',
            'Page Creator',
            '============',
            '',
        ]);

        /* Read parameters */
        $email = strval($input->getOption('email'));
        switch (true) {
            case $input->getOption('name') !== null:
                $calendarNameOrId = strval($input->getOption('name'));
                break;
            case $input->getOption('id') !== null:
                $calendarNameOrId = intval($input->getOption('id'));
                break;
            default:
                $output->writeln('At least one option for calendar is missing: id or name.'."\n");
                return Command::FAILURE;
        }

        $year = intval($input->getOption('year'));
        $month = intval($input->getOption('month'));
        $holidayGroupName = 'Saxony';

        /* Read calendar image and holiday group */
        $calendarImage = $this->calendarLoaderService->loadCalendarImageByCalendarNameYearAndMonth($email, $calendarNameOrId, $year, $month);
        $holidayGroup = $this->holidayGroupLoaderService->loadHolidayGroup($holidayGroupName);

        /* Print details */
        $output->writeln(sprintf('Email:          %s', $calendarImage->getUser()->getEmail()));
        $output->writeln(sprintf('Calendar name:  %s', $calendarImage->getCalendar()?->getName()));
        $output->writeln(sprintf('Year:           %d', $calendarImage->getYear()));
        $output->writeln(sprintf('Month:          %d', $calendarImage->getMonth()));
        $output->writeln(sprintf('Holiday group:  %s', $holidayGroup->getName()));

        $output->writeln('');
        $output->write(sprintf('Create calendar at %s. Please wait.. ', date('Y-m-d H:i:s')));

        /* Create calendar image */
        $timeStart = microtime(true);
        $calendarBuilderService = new CalendarBuilderService($this->appKernel);
        $calendarBuilderService->init($calendarImage, $holidayGroup, false, true, CalendarImage::QUALITY_TARGET);
        $file = $calendarBuilderService->build();
        $timeTaken = microtime(true) - $timeStart;

        $output->writeln(sprintf('→ Time taken: %.2fs', $timeTaken));

        $output->writeln('');
        $output->writeln('Calendar built from:');
        $output->writeln(sprintf('→ Path:      %s', $file['pathSource']));
        $output->writeln(sprintf('→ Mime:      %s', $file['mimeSource']));
        $output->writeln(sprintf('→ Size:      %s (%d Bytes)', $file['sizeHumanSource'], $file['sizeSource']));
        $output->writeln(sprintf('→ Dimension: %dx%d', $file['widthSource'], $file['heightSource']));

        $output->writeln('');
        $output->writeln('Calendar written to:');
        $output->writeln(sprintf('→ Path:      %s', $file['pathTarget']));
        $output->writeln(sprintf('→ Mime:      %s', $file['mimeTarget']));
        $output->writeln(sprintf('→ Size:      %s (%d Bytes)', $file['sizeHumanTarget'], $file['sizeTarget']));
        $output->writeln(sprintf('→ Dimension: %dx%d', $file['widthTarget'], $file['heightTarget']));

        $output->writeln('');

        return Command::SUCCESS;
    }
}
