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

use App\Constant\Code;
use App\DataType\Point;
use App\Repository\Base\PlaceRepositoryInterface;
use App\Repository\PlaceARepository;
use App\Repository\PlaceHRepository;
use App\Repository\PlaceLRepository;
use App\Repository\PlacePRepository;
use App\Repository\PlaceRRepository;
use App\Repository\PlaceSRepository;
use App\Repository\PlaceTRepository;
use App\Repository\PlaceURepository;
use App\Repository\PlaceVRepository;
use App\Service\Entity\PlaceLoaderService;
use App\Utils\Timer;
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
 * @version 0.1.2 (2022-11-22)
 * @since 0.1.2 (2022-11-22) Add PHP Magic Number Detector (PHPMND).
 * @since 0.1.1 (2022-11-11) PHPStan refactoring.
 * @since 0.1.0 (2022-05-08) First version.
 * @example bin/console app:coordinate:create [file]
 * @see http://download.geonames.org/export/dump/
 */
class CreateCoordinateCommand extends Command
{
    protected static $defaultName = 'app:coordinate:create';

    final public const COLS_EXPECTED = 19;

    private EntityManagerInterface $manager;

    protected PlaceARepository $placeARepository;

    protected PlaceHRepository $placeHRepository;

    protected PlaceLRepository $placeLRepository;

    protected PlacePRepository $placePRepository;

    protected PlaceRRepository $placeRRepository;

    protected PlaceSRepository $placeSRepository;

    protected PlaceTRepository $placeTRepository;

    protected PlaceURepository $placeURepository;

    protected PlaceVRepository $placeVRepository;

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $manager
     * @param PlaceARepository $placeARepository
     * @param PlaceHRepository $placeHRepository
     * @param PlaceLRepository $placeLRepository
     * @param PlacePRepository $placePRepository
     * @param PlaceRRepository $placeRRepository
     * @param PlaceSRepository $placeSRepository
     * @param PlaceTRepository $placeTRepository
     * @param PlaceURepository $placeURepository
     * @param PlaceVRepository $placeVRepository
     */
    public function __construct(EntityManagerInterface $manager, PlaceARepository $placeARepository, PlaceHRepository $placeHRepository, PlaceLRepository $placeLRepository, PlacePRepository $placePRepository, PlaceRRepository $placeRRepository, PlaceSRepository $placeSRepository, PlaceTRepository $placeTRepository, PlaceURepository $placeURepository, PlaceVRepository $placeVRepository)
    {
        parent::__construct();

        $this->manager = $manager;

        $this->placeARepository = $placeARepository;
        $this->placeHRepository = $placeHRepository;
        $this->placeLRepository = $placeLRepository;
        $this->placePRepository = $placePRepository;
        $this->placeRRepository = $placeRRepository;
        $this->placeSRepository = $placeSRepository;
        $this->placeTRepository = $placeTRepository;
        $this->placeURepository = $placeURepository;
        $this->placeVRepository = $placeVRepository;
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
     * Returns the place repository according to given feature class.
     *
     * @param string $featureClass
     * @return PlaceRepositoryInterface
     * @throws Exception
     */
    protected function getPlaceRepository(string $featureClass): PlaceRepositoryInterface
    {
        return match ($featureClass) {
            Code::FEATURE_CLASS_A => $this->placeARepository,
            Code::FEATURE_CLASS_H => $this->placeHRepository,
            Code::FEATURE_CLASS_L => $this->placeLRepository,
            Code::FEATURE_CLASS_P => $this->placePRepository,
            Code::FEATURE_CLASS_R => $this->placeRRepository,
            Code::FEATURE_CLASS_S => $this->placeSRepository,
            Code::FEATURE_CLASS_T => $this->placeTRepository,
            Code::FEATURE_CLASS_U => $this->placeURepository,
            Code::FEATURE_CLASS_V => $this->placeVRepository,
            default => throw new Exception(sprintf('Unexpected feature class "%s" (%s:%d).', $featureClass, __FILE__, __LINE__)),
        };
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

        $timer = Timer::start();

        $handle = fopen($file, 'r');

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

            if (count($row) !== self::COLS_EXPECTED) {
                $output->writeln(sprintf('%d/%d (%.2f%%): Line "%d" ignored (wrong format).', $currentLine, $numberOfLines, $percent, $currentLine));
                continue;
            }

            /* Get some data. */
            $geonameId = intval($row[0]);
            $name = strval($row[1]);
            $featureClass = strval($row[6]);

            $placeRepository = $this->getPlaceRepository($featureClass);

            $place = $placeRepository->findOneBy(['geonameId' => $geonameId]);

            if ($place === null) {
                $place = PlaceLoaderService::getPlace($featureClass);
            }

            $point = new Point(floatval($row[4]), floatval($row[5]));

            $dateSplit = explode('-', strval($row[18]));
            $date = new DateTime();
            $date->setDate(intval($dateSplit[0]), intval($dateSplit[1]), intval($dateSplit[2]));

            $place->setGeonameId($geonameId);
            $place->setName($name);
            $place->setAsciiName(strval($row[2]));
            $place->setAlternateNames(strval($row[3]));
            $place->setCoordinate($point);
            $place->setFeatureClass($featureClass);
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
