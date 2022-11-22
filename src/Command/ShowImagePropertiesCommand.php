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

use App\Service\Entity\PlaceLoaderService;
use App\Service\ImageDataService;
use App\Service\LocationDataService;
use App\Utils\Image\Color;
use App\Utils\Image\ColorDetectorCiede2000;
use App\Utils\Image\ColorDetectorSimple;
use App\Utils\Image\Palette;
use App\Utils\Timer;
use Exception;
use GdImage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowImagePropertiesCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-22)
 * @since 0.1.1 (2022-11-22) Add PHP Magic Number Detector (PHPMND).
 * @since 0.1.0 (2022-04-29) First version.
 * @package App\Command
 * @example bin/console app:image:show "path"
 * @see https://github.com/posva/catimg
 */
class ShowImagePropertiesCommand extends Command
{
    protected static $defaultName = 'app:image:show';

    protected const REGEXP_OUTPUT = '%%-%ds %%-%ds %%s';

    protected const NAME_TRANSPARENT = 'transparent';

    protected const DEFAULT_IMAGE_WIDTH = 120;

    protected const DEFAULT_COLOR_COUNT = 5;

    /* @see https://www.php.net/manual/de/function.imagesetinterpolation.php */
    protected const DEFAULT_IMAGE_MODE = IMG_GAUSSIAN;

    protected const LINE_BREAK = "\n";

    protected bool $debug = false;

    protected bool $verbose = false;

    /**
     * ShowImagePropertiesCommand constructor.
     */
    public function __construct(protected PlaceLoaderService $placeLoaderService, protected LocationDataService $locationDataService)
    {
        parent::__construct();
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this
            ->setName('app:image:show')
            ->setDescription('Shows image properties.')
            ->setDefinition([
                new InputArgument('path', InputArgument::REQUIRED, 'The path to image.'),
                new InputOption('debug', 'd', InputOption::VALUE_NONE, 'Switch to debug mode.'),
            ])
            ->setHelp(
                <<<'EOT'

The <info>app:image:show</info> shows image properties:
  <info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * Returns the output lines from image data.
     *
     * @param ImageDataService $imageData
     * @return string[]
     * @throws Exception
     */
    protected function getOutputLines(ImageDataService $imageData): array
    {
        $dataImage = $imageData->getImageDataFull();

        $outputLines = [];

        foreach ($dataImage as $key => $data) {
            $valueFormatted = strval($data[ImageDataService::KEY_NAME_VALUE_FORMATTED]);

            $format = sprintf(self::REGEXP_OUTPUT, ImageDataService::WIDTH_TITLE, ImageDataService::WIDTH_TITLE);
            $outputLine = sprintf($format, strval($data[ImageDataService::KEY_NAME_TITLE]), $key, $valueFormatted);

            $outputLines[] = $outputLine;
        }

        return $outputLines;
    }

    /**
     * Returns the max length of given array.
     *
     * @param string[] $lines
     * @return int
     */
    protected function getMaxLength(array $lines): int
    {
        $outputMaxLength = 0;

        foreach ($lines as $line) {
            if (strlen($line) > $outputMaxLength) {
                $outputMaxLength = strlen($line);
            }
        }

        return $outputMaxLength;
    }

    /**
     * Resize given GdImage.
     *
     * @param GdImage $gdImage
     * @param int $width
     * @param int $mode
     * @return GdImage
     * @throws Exception
     */
    protected function resizeImageGd(GdImage $gdImage, int $width = self::DEFAULT_IMAGE_WIDTH, int $mode = self::DEFAULT_IMAGE_MODE): GdImage
    {
        $gdImageResized = imagescale($gdImage, $width, -1, $mode);

        if ($gdImageResized === false) {
            throw new Exception(sprintf('Unable to resize given image (%s:%d).', __FILE__, __LINE__));
        }

        return $gdImageResized;
    }

    /**
     * Converts given image to string.
     *
     * @param GdImage $gdImage
     * @return string
     * @throws Exception
     */
    protected function convertImageToString(GdImage $gdImage): string
    {
        $width = imagesx($gdImage);
        $height = imagesy($gdImage);

        $image = '';
        for ($y = 0; $y < floor($height / 2); $y++) {
            for ($x = 0; $x < $width; $x++) {
                $yTop = 2 * $y;
                $yBottom = 2 * $y + 1;

                $colorTop = imagecolorat($gdImage, $x, $yTop);
                $colorBottom = $yBottom + 1 <= $height ? imagecolorat($gdImage, $x, $yBottom) : null;

                if ($colorTop === false) {
                    throw new Exception(sprintf('Unable to get pixel (%s:%d).', __FILE__, __LINE__));
                }
                if ($colorBottom === false) {
                    throw new Exception(sprintf('Unable to get pixel (%s:%d).', __FILE__, __LINE__));
                }

                $image .= $this->get1x2Pixel(
                    Color::convertIntToHex($colorTop),
                    $colorBottom === null ? self::NAME_TRANSPARENT : Color::convertIntToHex($colorBottom)
                );
            }
            $image .= self::LINE_BREAK;
        }

        return $image;
    }

    /**
     * Prints image to screen.
     *
     * @param string $imagePath
     * @param OutputInterface $output
     * @param int $width
     * @param int $mode
     * @return GdImage
     * @throws Exception
     */
    protected function printImageGd(string $imagePath, OutputInterface $output, int $width = self::DEFAULT_IMAGE_WIDTH, int $mode = self::DEFAULT_IMAGE_MODE): GdImage
    {
        $timer = Timer::start();

        $gdImage = $this->resizeImageGd($this->createGdImageFromGivenPath($imagePath), $width, $mode);

        $imageString = $this->convertImageToString($gdImage);

        $time = Timer::stop($timer);

        $title = sprintf('Image (%.4fs)', $time);

        $output->writeln('');
        $output->writeln($title);
        $output->writeln(str_repeat('-', strlen($title)));
        $output->writeln('');
        $output->writeln($imageString);
        $output->writeln('');

        return $gdImage;
    }

    /**
     * Prints the colors of image (ciede 2000).
     *
     * @param GdImage $gdImage
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function printColorsCiede2000(GdImage $gdImage, OutputInterface $output): void
    {
        $timer = Timer::start();

        $palette = Palette::createPaletteFromGdImage($gdImage);

        $colorDetector = new ColorDetectorCiede2000($palette);

        $colors = $colorDetector->extract(self::DEFAULT_COLOR_COUNT);

        $time = Timer::stop($timer);

        $colorText = '';
        foreach ($colors as $color) {
            $colorHex = Color::convertIntToHex($color);
            $colorText .= $this->get1x2Pixel($colorHex, $colorHex, 2);
            $colorText .= ' ';
        }

        $title = sprintf('Image colors - Ciede 2000 (%.4fs)', $time);

        $output->writeln('');
        $output->writeln($title);
        $output->writeln(str_repeat('-', strlen($title)));
        $output->writeln('');
        $output->writeln($colorText);
        $output->writeln('');
        $output->writeln('');
    }

    /**
     * Prints the colors of image.
     *
     * @param GdImage $gdImage
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function printColorsSimple(GdImage $gdImage, OutputInterface $output): void
    {
        $timer = Timer::start();

        $colorMostCommon = new ColorDetectorSimple($gdImage);

        $colors = $colorMostCommon->getColors(self::DEFAULT_COLOR_COUNT, ColorDetectorSimple::REDUCE_BRIGHTNESS_DEFAULT, ColorDetectorSimple::REDUCE_GRADIENTS_DEFAULT, ColorDetectorSimple::DELTA_DEFAULT);

        $time = Timer::stop($timer);

        $colorText = '';
        foreach ($colors as $colorHex => $frequency) {
            $colorText .= $this->get1x2Pixel('#'.strval($colorHex), '#'.strval($colorHex), 2);
            $colorText .= ' ';
        }

        $title = sprintf('Image colors - Simple (%.4fs)', $time);

        $output->writeln('');
        $output->writeln($title);
        $output->writeln(str_repeat('-', strlen($title)));
        $output->writeln('');
        $output->writeln($colorText);
        $output->writeln('');
        $output->writeln('');
    }

    /**
     * Gets image info.
     *
     * @param string $path
     * @return string[]|int[]
     * @throws Exception
     */
    protected function getImageInfo(string $path): array
    {
        /* Get information about image. */
        $imageInfo = getimagesize($path);

        if ($imageInfo === false) {
            throw new Exception(sprintf('Unable to get image information from "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        return $imageInfo;
    }

    /**
     * Creates image from given path.
     *
     * @param string $path
     * @return GdImage
     * @throws Exception
     */
    protected function createGdImageFromGivenPath(string $path): GdImage
    {
        $imageInfo = $this->getImageInfo($path);

        /* Create image. */
        $gdImage = match ($imageInfo[2]) {
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            default => throw new Exception(sprintf('Unsupported image type %d - %s (%s:%d)', $imageInfo[2], $imageInfo['mime'], __FILE__, __LINE__)),
        };

        if ($gdImage === false) {
            throw new Exception(sprintf('Unable to load image (%s:%d).', __FILE__, __LINE__));
        }

        return $gdImage;
    }

    /**
     * Prints image data.
     *
     * @param string $imagePath
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function printImageData(string $imagePath, OutputInterface $output): void
    {
        $timer = Timer::start();

        $imageData = new ImageDataService(
            $imagePath,
            $this->placeLoaderService,
            $this->locationDataService,
            $this->debug,
            $this->verbose
        );

        $outputLines = $this->getOutputLines($imageData);
        $outputMaxLength = $this->getMaxLength($outputLines);

        $format = sprintf(self::REGEXP_OUTPUT, ImageDataService::WIDTH_TITLE, ImageDataService::WIDTH_TITLE);

        $imageDataText = sprintf($format, 'Title', 'Key', 'Value').self::LINE_BREAK;
        $imageDataText .= str_repeat('-', $outputMaxLength).self::LINE_BREAK;
        foreach ($outputLines as $outputLine) {
            $imageDataText .= $outputLine.self::LINE_BREAK;
        }

        $time = Timer::stop($timer);

        $title = sprintf('Image properties (%.4fs)', $time);

        $output->writeln('');
        $output->writeln($title);
        $output->writeln(str_repeat('-', strlen($title)));
        $output->writeln('');
        $output->writeln($imageDataText);
        $output->writeln('');
        $output->writeln('');
    }

    /**
     * Prints 1x2 pixel.
     *
     * @param string $colorTop
     * @param string|null $colorBottom
     * @param int $repeat
     * @return string
     * @throws Exception
     */
    protected function get1x2Pixel(string $colorTop, ?string $colorBottom = null, int $repeat = 1): string
    {
        if ($colorBottom === null) {
            $colorBottom = $colorTop;
        }

        if ($colorTop !== self::NAME_TRANSPARENT && !preg_match('~^#[a-f0-9]{6,6}$~i', $colorTop)) {
            throw new Exception(sprintf('Unexpected color given "%s" (%s:%d).', $colorTop, __FILE__, __LINE__));
        }
        if ($colorBottom !== self::NAME_TRANSPARENT && !preg_match('~^#[a-f0-9]{6,6}$~i', $colorBottom)) {
            throw new Exception(sprintf('Unexpected color given "%s" (%s:%d).', $colorBottom, __FILE__, __LINE__));
        }

        switch (true) {
            case $colorTop === self::NAME_TRANSPARENT && $colorBottom === self::NAME_TRANSPARENT:
                return str_repeat(' ', $repeat);

            case $colorTop === self::NAME_TRANSPARENT:
                $rgb = Color::convertHexToRgbArray($colorTop);
                return sprintf("\x1b[38;2;%d;%d;%dm%s\x1b[0m", $rgb['r'], $rgb['g'], $rgb['b'], str_repeat('▄', $repeat));

            case $colorBottom === self::NAME_TRANSPARENT:
                $rgb = Color::convertHexToRgbArray($colorTop);
                return sprintf("\x1b[38;2;%d;%d;%dm%s\x1b[0m", $rgb['r'], $rgb['g'], $rgb['b'], str_repeat('▀', $repeat));

            default:
                $rgbTop = Color::convertHexToRgbArray($colorTop);
                $rgbBottom = Color::convertHexToRgbArray($colorBottom);
                return sprintf(
                    "\x1b[38;2;%d;%d;%dm\x1b[48;2;%d;%d;%dm%s\x1b[0m",
                    $rgbTop['r'],
                    $rgbTop['g'],
                    $rgbTop['b'],
                    $rgbBottom['r'],
                    $rgbBottom['g'],
                    $rgbBottom['b'],
                    str_repeat('▀', $repeat)
                );
        }
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
        $this->debug = boolval($input->getOption('debug'));
        $this->verbose = boolval($input->getOption('verbose'));

        /* Read parameter. */
        $imagePath = strval($input->getArgument('path'));

        if (!file_exists($imagePath)) {
            $output->writeln(sprintf('Unable to find image file "%s".', $imagePath));
            return Command::INVALID;
        }

        /* Print image. */
        $this->printImageGd($imagePath, $output);

        $gdImage = $this->resizeImageGd($this->createGdImageFromGivenPath($imagePath), 150, self::DEFAULT_IMAGE_MODE);

        /* Print colors. */
        $this->printColorsCiede2000($gdImage, $output);

        $this->printColorsSimple($gdImage, $output);

        /* Print image data. */
        $this->printImageData($imagePath, $output);

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
