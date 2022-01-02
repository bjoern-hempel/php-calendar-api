<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2022 Björn Hempel <bjoern@hempel.li>
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

use App\Entity\CalendarImage;
use App\Service\CalendarBuilderService;
use App\Service\CalendarLoaderService;
use App\Service\HolidayGroupLoaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateCalendarCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-02)
 * @package App\Command
 * @example bin/console calendar:create-calendar --email "user1@domain.tld" --name "Calendar 1"
 */
class CreateCalendarCommand extends Command
{
    protected static $defaultName = 'calendar:create-calendar';

    protected CalendarBuilderService $calendarBuilderService;

    protected CalendarLoaderService $calendarLoaderService;

    protected HolidayGroupLoaderService $holidayGroupLoaderService;

    protected EntityManagerInterface $manager;

    /**
     * CreatePageCommand constructor
     *
     * @param CalendarBuilderService $calendarBuilderService
     * @param CalendarLoaderService $calendarLoaderService
     * @param HolidayGroupLoaderService $holidayGroupLoaderService
     * @param EntityManagerInterface $manager
     */
    public function __construct(CalendarBuilderService $calendarBuilderService, CalendarLoaderService $calendarLoaderService, HolidayGroupLoaderService $holidayGroupLoaderService, EntityManagerInterface $manager)
    {
        $this->calendarBuilderService = $calendarBuilderService;

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
        if (CreateCalendarCommand::$defaultName !== null) {
            $this->setName(CreateCalendarCommand::$defaultName);
        }

        $this
            ->setDescription('Creates the whole calendar')
            ->setDefinition([
                new InputOption('email', null, InputOption::VALUE_REQUIRED, 'The email of the user.'),
                new InputOption('name', null, InputOption::VALUE_REQUIRED, 'The calendar name which will be created.'),
            ])
            ->setHelp(
                <<<'EOT'
The <info>calendar:create-calendar</info> creates a calendar page:
  <info>php %command.full_name%</info>
Creates the whole calendar.
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
            '================',
            'Calendar Creator',
            '================',
            '',
        ]);

        /* Read parameter */
        $email = strval($input->getOption('email'));
        $calendarName = strval($input->getOption('name'));

        /* Read db */
        $calendar = $this->calendarLoaderService->loadCalendar($email, $calendarName);

        /* Get application */
        $application = $this->getApplication();

        /* Check application */
        if ($application === null) {
            throw new Exception(sprintf('Can not find any application (%s:%d).', __FILE__, __LINE__));
        }

        /* Get command */
        $command = $application->find('calendar:create-page');

        /** @var CalendarImage $calendarImage */
        foreach ($calendar->getCalendarImages() as $calendarImage) {
            $calendarCreatePageInput = new ArrayInput([
                '--email' => $calendar->getUser()->getEmail(),
                '--name' => $calendar->getName(),
                '--year'  => $calendarImage->getYear(),
                '--month' => $calendarImage->getMonth(),
            ]);

            $returnCode = $command->run($calendarCreatePageInput, $output);

            if ($returnCode !== Command::SUCCESS) {
                $output->writeln('An error occurred while trying to create a single calendar page.');

                return $returnCode;
            }
        }

        return Command::SUCCESS;
    }
}
