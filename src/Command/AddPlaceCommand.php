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
use App\Service\LocationDataService;
use App\Utils\GPSConverter;
use App\Utils\Timer;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AddPlaceCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-31)
 * @package App\Command
 * @example bin/console app:coordinate:create [file]
 * @see http://download.geonames.org/export/dump/
 */
class AddPlaceCommand extends Command
{
    protected static $defaultName = 'app:place:add';

    protected EntityManagerInterface $manager;

    protected TranslatorInterface $translator;

    protected LocationDataService $locationDataService;

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
     * @param TranslatorInterface $translator
     * @param LocationDataService $locationDataService
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
    public function __construct(EntityManagerInterface $manager, TranslatorInterface $translator, LocationDataService $locationDataService, PlaceARepository $placeARepository, PlaceHRepository $placeHRepository, PlaceLRepository $placeLRepository, PlacePRepository $placePRepository, PlaceRRepository $placeRRepository, PlaceSRepository $placeSRepository, PlaceTRepository $placeTRepository, PlaceURepository $placeURepository, PlaceVRepository $placeVRepository)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->translator = $translator;
        $this->locationDataService = $locationDataService;

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
                new InputArgument('feature-class', InputArgument::REQUIRED, 'The feature class'),
                new InputArgument('feature-code', InputArgument::REQUIRED, 'The feature code'),
                new InputArgument('country-code', InputArgument::REQUIRED, 'The country code'),
                new InputArgument('latitude', InputArgument::REQUIRED, 'The latitude (y position).'),
                new InputArgument('longitude', InputArgument::REQUIRED, 'The longitude (x position).'),
                new InputArgument('name', InputArgument::REQUIRED, 'The name'),
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
        $featureClass = strval($input->getArgument('feature-class'));
        $featureCode = strval($input->getArgument('feature-code'));
        $countryCode = strval($input->getArgument('country-code'));
        $latitude = floatval($input->getArgument('latitude'));
        $longitude = floatval($input->getArgument('longitude'));
        $name = strval($input->getArgument('name'));

        $translationCode = sprintf('%s.%s', $featureClass, $featureCode);
        $translation = $this->translator->trans($translationCode, [], 'place');

        if ($translationCode === $translation) {
            $output->writeln(sprintf('Unable to find feature class "%s" with feature code "%s".', $featureClass, $featureCode));
            return Command::INVALID;
        }

        $placeRepository = $this->getPlaceRepository($featureClass);

        $place = $placeRepository->findOneBy(['name' => $name, 'countryCode' => $countryCode]);

        $additionalQuestion = $place !== null ? ' (already exists)' : '';

        $googleLink = GPSConverter::decimalDegree2google($latitude, $longitude);
        $question = sprintf('Add this place%s: "%s" (FClass = %s, FCode = %s, CCode = %s, Google = "%s")? ', $additionalQuestion, $name, $featureClass, $featureCode, $countryCode, $googleLink);

        $helper = $this->getHelper('question');

        if (!$helper instanceof QuestionHelper) {
            throw new Exception(sprintf('Unable to get question (%s:%d).', __FILE__, __LINE__));
        }

        $question = new ConfirmationQuestion($question, false);

        /* Cancel task (answer "no" given). */
        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $locationPlace = $this->locationDataService->getLocationPlace($latitude, $longitude);

        if ($locationPlace === null) {
            $output->writeln(sprintf('I can not find that place (%f, %f).".', $latitude, $longitude));
            return Command::INVALID;
        }

        $lowestId = 100000000;
        $geonameId = $placeRepository->getHighestGeonameId();
        $geonameId++;

        if ($geonameId < $lowestId) {
            $geonameId = $lowestId;
        }

        $point = new Point($latitude, $longitude);
        $place = PlaceLoaderService::getPlace($featureClass);

        $place->setGeonameId($geonameId);
        $place->setName($name);
        $place->setAsciiName($name);
        $place->setAlternateNames($name);
        $place->setCoordinate($point);
        $place->setFeatureClass($featureClass);
        $place->setFeatureCode($featureCode);
        $place->setCountryCode($countryCode);
        $place->setCc2('');
        $place->setAdmin1Code($locationPlace->getAdmin1Code());
        $place->setAdmin2Code($locationPlace->getAdmin2Code());
        $place->setAdmin3Code($locationPlace->getAdmin3Code());
        $place->setAdmin4Code($locationPlace->getAdmin4Code());
        $place->setPopulation(0);
        $place->setElevation($locationPlace->getElevation());
        $place->setDem($locationPlace->getDem());
        $place->setTimezone($locationPlace->getTimezone());
        $place->setModificationDate(new DateTime());
        $place->setCreatedAt(new DateTimeImmutable());
        $place->setUpdatedAt(new DateTimeImmutable());

        $this->manager->persist($place);
        $this->manager->flush();

        $output->writeln('');
        $output->writeln(sprintf('"%s" written.', $name));

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}