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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    /** @var string[][]|int[][] $config  */
    protected array $config = [
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

    protected float $factor = 1.414;

    protected CalendarBuilderService $calendarBuilderService;

    /**
     * CreatePageCommand constructor
     *
     * @param CalendarBuilderService $calendarBuilderService
     */
    public function __construct(CalendarBuilderService $calendarBuilderService)
    {
        $this->calendarBuilderService = $calendarBuilderService;

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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Page Creator',
            '============',
            '',
        ]);

        // retrieve the argument value using getArgument()
        $output->writeln('Year:  ' . $input->getOption('year'));
        $output->writeln('Month: ' . $input->getOption('month'));

        if (!array_key_exists(strval($input->getOption('month')), $this->config)) {
            $output->writeln(sprintf('%s does not exist.', strval($input->getOption('month'))));
        }

        $config = $this->config[$input->getOption('month')];

        $this->calendarBuilderService->init(intval($config['height']), $this->factor);
        $this->calendarBuilderService->build(
            strval($config['sourcePath']),
            strval($config['targetPath']),
            strval($config['title']),
            strval($config['position']),
            intval($config['year']),
            intval($config['month']),
            intval($config['valign'])
        );

        return Command::SUCCESS;
    }
}
