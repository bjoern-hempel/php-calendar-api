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

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\Image;
use App\Entity\User;
use App\Repository\CalendarImageRepository;
use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use App\Service\CalendarBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
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

    protected float $factor = 1.414;

    protected CalendarBuilderService $calendarBuilderService;

    protected EntityManagerInterface $manager;

    /**
     * CreatePageCommand constructor
     *
     * @param CalendarBuilderService $calendarBuilderService
     * @param EntityManagerInterface $manager
     */
    public function __construct(CalendarBuilderService $calendarBuilderService, EntityManagerInterface $manager)
    {
        $this->calendarBuilderService = $calendarBuilderService;

        $this->manager = $manager;

        parent::__construct();
    }

    /**
     * Returns the DocumentRepository.
     *
     * @return UserRepository
     * @throws Exception
     */
    protected function getUserRepository(): UserRepository
    {
        $repository = $this->manager->getRepository(User::class);

        if (!$repository instanceof UserRepository) {
            throw new Exception('Error while getting UserRepository.');
        }

        return $repository;
    }

    /**
     * Returns the CalendarRepository.
     *
     * @return CalendarRepository
     * @throws Exception
     */
    protected function getCalendarRepository(): CalendarRepository
    {
        $repository = $this->manager->getRepository(Calendar::class);

        if (!$repository instanceof CalendarRepository) {
            throw new Exception('Error while getting CalendarRepository.');
        }

        return $repository;
    }

    /**
     * Returns the CalendarImageRepository.
     *
     * @return CalendarImageRepository
     * @throws Exception
     */
    protected function getCalendarImageRepository(): CalendarImageRepository
    {
        $repository = $this->manager->getRepository(CalendarImage::class);

        if (!$repository instanceof CalendarImageRepository) {
            throw new Exception('Error while getting CalendarImageRepository.');
        }

        return $repository;
    }

    /**
     * Returns user and calendar image.
     *
     * @param string $email
     * @param string $name
     * @param int $year
     * @param int $month
     * @return array{user: User, calendar: Calendar, calendar-image: CalendarImage, image: Image}
     * @throws NonUniqueResultException
     * @throws Exception
     */
    #[ArrayShape(['user' => 'User', 'calendar' => 'Calendar', 'calendar-image' => 'CalendarImage', 'image' => 'Image'])]
    protected function getUserAndCalendarImage(string $email, string $name, int $year, int $month): array
    {
        $user = $this->getUserRepository()->findOneByEmail($email);
        if ($user === null) {
            throw new Exception(sprintf('Unable to find user with email "%s".', $email));
        }

        $calendar = $this->getCalendarRepository()->findOneByName($user, $name);
        if ($calendar === null) {
            throw new Exception(sprintf('Unable to find calendar with name "%s".', $name));
        }

        $calendarImage = $this->getCalendarImageRepository()->findOneByYearAndMonth($user, $calendar, $year, $month);
        if ($calendarImage === null) {
            throw new Exception(sprintf('Unable to find calendar image with year "%d" and month "%d".', $year, $month));
        }

        $image = $calendarImage->getImage();
        if ($image === null) {
            throw new Exception('Unable to find image.');
        }

        return [
            'user' => $user,
            'calendar' => $calendar,
            'calendar-image' => $calendarImage,
            'image' => $image,
        ];
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
        $name = 'Name 1';
        $year = intval($input->getOption('year'));
        $month = intval($input->getOption('month'));

        $data = $this->getUserAndCalendarImage($email, $name, $year, $month);

        /** @var CalendarImage $calendarImage */
        $calendarImage = $data['calendar-image'];

        /** @var Image $image */
        $image = $data['image'];

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
