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

namespace App\Command;

use App\Service\CalendarBuilderService;
use App\Service\CalendarLoaderService;
use App\Service\HolidayGroupLoaderService;
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
                new InputOption('name', null, InputOption::VALUE_REQUIRED, 'The calendar name which will be used.'),
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

        /* Read parameter */
        $email = strval($input->getOption('email'));
        $calendarName = strval($input->getOption('name'));
        $year = intval($input->getOption('year'));
        $month = intval($input->getOption('month'));
        $holidayGroupName = 'Saxony';

        /* Read db */
        $calendarImage = $this->calendarLoaderService->loadCalendarImage($email, $calendarName, $year, $month);
        $holidayGroup = $this->holidayGroupLoaderService->loadHolidayGroup($holidayGroupName);

        /* Print details */
        $output->writeln(sprintf('Email:          %s', $calendarImage->getUser()->getEmail()));
        $output->writeln(sprintf('Calendar name:  %s', $calendarImage->getCalendar()->getName()));
        $output->writeln(sprintf('Year:           %d', $calendarImage->getYear()));
        $output->writeln(sprintf('Month:          %d', $calendarImage->getMonth()));
        $output->writeln(sprintf('Holiday group:  %s', $holidayGroup->getName()));

        $output->writeln('');
        $output->write(sprintf('Create calendar at %s. Please wait.. ', date('Y-m-d H:i:s')));

        /* Create calendar image */
        $timeStart = microtime(true);
        $calendarBuilderService = new CalendarBuilderService($this->appKernel);
        $calendarBuilderService->init($calendarImage, $holidayGroup);
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
