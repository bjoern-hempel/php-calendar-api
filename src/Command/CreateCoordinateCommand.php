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

use App\Entity\Place;
use App\Repository\PlaceRepository;
use App\Utils\Timer;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

/**
 * Class CreateCoordinateCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-08)
 * @package App\Command
 * @example bin/console app:coordinate:create [file]
 * @see http://download.geonames.org/export/dump/
 */
class CreateCoordinateCommand extends Command
{
    protected static $defaultName = 'app:coordinate:create';

    private EntityManagerInterface $manager;

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this
            ->setName(strval(self::$defaultName))
            ->setDescription('Creates coordinates from given file.')
            ->setDefinition([
                new InputArgument('file', InputArgument::REQUIRED, 'The file'),
            ])
            ->setHelp(
                <<<'EOT'

The <info>app:coordinate:create</info> command creates coordinates from given file.

EOT
            );
    }

    /**
     * Execute the commands.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* Read parameter. */
        $file = strval($input->getArgument('file'));

        /** @var PlaceRepository $placeRepository */
        $placeRepository = $this->manager->getRepository(Place::class);

        $timer = Timer::start();

        $handle = fopen($file, "r") or die("Couldn't get handle");

        if ($handle === false) {
            throw new Exception(sprintf('Unable to get ressource (%s:%d).', __FILE__, __LINE__));
        }

        $numberOfLines = 10000;

        $currentLine = 0;
        while (!feof($handle)) {
            $currentLine++;

            $buffer = fgets($handle, 4096);

            $percent = $currentLine / $numberOfLines * 100;

            if ($buffer === false) {
                $output->writeln(sprintf('%d/%d (%.2f%%): Line "%d" ignored (empty buffer).', $currentLine, $numberOfLines, $percent, $currentLine));
                continue;
            }

            if (empty($buffer)) {
                $output->writeln(sprintf('%d/%d (%.2f%%): Line "%d" ignored (empty buffer).', $currentLine, $numberOfLines, $percent, $currentLine));
                continue;
            }

            $row = str_getcsv($buffer, "\t", '\'');

            if (count($row) !== 19) {
                $output->writeln(sprintf('%d/%d (%.2f%%): Line "%d" ignored (wrong format).', $currentLine, $numberOfLines, $percent, $currentLine));
                continue;
            }

            $geonameId = intval($row[0]);
            $name = strval($row[1]);

            $place = $placeRepository->findOneBy(['geonameId' => $geonameId]);

            if (!$place instanceof Place) {
                $place = new Place();
            }

            $point = new Point(floatval($row[4]), floatval($row[5]));

            $dateSplit = explode('-', $row[18]);
            $date = new DateTime();
            $date->setDate(intval($dateSplit[0]), intval($dateSplit[1]), intval($dateSplit[2]));

            $place->setGeonameId($geonameId);
            $place->setName($name);
            $place->setAsciiName(strval($row[2]));
            $place->setAlternateNames(strval($row[3]));
            $place->setCoordinate($point);
            $place->setFeatureClass(strval($row[6]));
            $place->setFeatureCode(strval($row[7]));
            $place->setCountryCode(strval($row[8]));
            $place->setCc2(strval($row[9]));

            if (!empty($row[10])) {
                $place->setAdmin1Code($row[10]);
            }

            if (!empty($row[11])) {
                $place->setAdmin2Code($row[11]);
            }

            if (!empty($row[12])) {
                $place->setAdmin3Code($row[12]);
            }

            if (!empty($row[13])) {
                $place->setAdmin4Code($row[13]);
            }

            $place->setPopulation(strval($row[14]));
            $place->setElevation(intval($row[15]));
            $place->setDem(intval($row[16]));
            $place->setTimezone(strval($row[17]));
            $place->setModificationDate($date);
            $place->setCreatedAt(new DateTimeImmutable());
            $place->setUpdatedAt(new DateTimeImmutable());

            $this->manager->persist($place);
            $this->manager->flush();

            $output->writeln(sprintf('%d/%d (%.2f%%): Record "%d" - "%s" written', $currentLine, $numberOfLines, $percent, $geonameId, $name));
        }
        fclose($handle);

        $output->writeln('');
        $output->writeln(sprintf('Finished (%.2fs).', Timer::time($timer)));

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
