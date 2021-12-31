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

namespace App\Command;

use App\Service\CalendarBuilderService;
use App\Service\CalendarLoaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePageCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-29)
 * @package App\Command
 */
class CreatePageCommand extends Command
{
    protected static $defaultName = 'calendar:create-page';

    protected float $factor = 1.414;

    protected CalendarBuilderService $calendarBuilderService;

    protected CalendarLoaderService $calendarLoaderService;

    protected EntityManagerInterface $manager;

    /**
     * CreatePageCommand constructor
     *
     * @param CalendarBuilderService $calendarBuilderService
     * @param CalendarLoaderService $calendarLoaderService
     * @param EntityManagerInterface $manager
     */
    public function __construct(CalendarBuilderService $calendarBuilderService, CalendarLoaderService $calendarLoaderService, EntityManagerInterface $manager)
    {
        $this->calendarBuilderService = $calendarBuilderService;

        $this->calendarLoaderService = $calendarLoaderService;

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
            'Page Creator',
            '============',
            '',
        ]);

        $email = 'user1@domain.tld';
        $name = 'Calendar 1';
        $year = intval($input->getOption('year'));
        $month = intval($input->getOption('month'));

        $this->calendarLoaderService->loadCalendarImage($email, $name, $year, $month);

        $image = $this->calendarLoaderService->getImage();
        $calendarImage = $this->calendarLoaderService->getCalendarImage();

        // retrieve the argument value using getArgument()
        $output->writeln(sprintf('Year:  %d',$year));
        $output->writeln(sprintf('Month: %d', $month));

        /* Create calendar image */
        $this->calendarBuilderService->init($image->getHeight() ?? 4000, $this->factor);
        $this->calendarBuilderService->build(
            $image->getSourcePath(),
            $image->getTargetPath(),
            $calendarImage->getTitle() ?? '',
            $calendarImage->getPosition() ?? '',
            $year,
            $month,
            $calendarImage->getValign() ?? CalendarBuilderService::VALIGN_TOP
        );

        return Command::SUCCESS;
    }
}
