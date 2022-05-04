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

use App\Utils\Image\Color;
use App\Utils\ImageData;
use Exception;
use GdImage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowImagePropertiesCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-29)
 * @package App\Command
 * @example bin/console app:image:show "path"
 * @see https://github.com/posva/catimg
 */
class ShowImagePropertiesCommand extends Command
{
    protected static $defaultName = 'app:image:show';

    protected const REGEXP_OUTPUT = '%%-%ds %%-%ds %%s%s%%s';

    protected const NAME_TRANSPARENT = 'transparent';

    protected const DEFAULT_INTERMEDIATE_STEP = 1.0;

    protected const DEFAULT_IMAGE_WIDTH = 120;

    /* @see https://www.php.net/manual/de/function.imagesetinterpolation.php */
    protected const DEFAULT_IMAGE_MODE = IMG_GAUSSIAN;

    /**
     * ShowImagePropertiesCommand constructor.
     */
    public function __construct()
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
                new InputArgument('path', InputArgument::REQUIRED, 'The path to image'),
            ])
            ->setHelp(
                <<<'EOT'

The <info>app:image:show</info> shows image properties:
  <info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * Returns if given command application exists.
     *
     * @param string $cmd
     * @return bool
     */
    protected function commandExist(string $cmd): bool
    {
        $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));

        return !empty($return);
    }

    /**
     * Returns the output lines from image data.
     *
     * @param ImageData $imageData
     * @return string[]
     * @throws Exception
     */
    protected function getOutputLines(ImageData $imageData): array
    {
        $dataImage = $imageData->getDataImage();

        $outputLines = [];

        foreach ($dataImage as $key => $data) {
            $value = $data[ImageData::KEY_NAME_VALUE];

            if (!is_bool($value) && !is_float($value) && !is_int($value) && !is_string($value) && !is_null($value)) {
                throw new Exception(sprintf('Unsupported type "%s" given (%s:%d).', gettype($value), __FILE__, __LINE__));
            }

            $format = sprintf(self::REGEXP_OUTPUT, ImageData::WIDTH_TITLE, ImageData::WIDTH_TITLE, strval($data[ImageData::KEY_NAME_FORMAT]));
            $outputLine = sprintf($format, strval($data[ImageData::KEY_NAME_TITLE]), $key, strval($data[ImageData::KEY_NAME_UNIT_BEFORE]), $value, strval($data[ImageData::KEY_NAME_UNIT]));

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
     * Prints image to screen.
     *
     * @param string $imagePath
     * @param OutputInterface $output
     * @param int $width
     * @param int $mode
     * @return void
     * @throws Exception
     */
    protected function printImageGd(string $imagePath, OutputInterface $output, int $width = self::DEFAULT_IMAGE_WIDTH, int $mode = self::DEFAULT_IMAGE_MODE): void
    {
        $output->writeln('');
        $output->writeln('Image (Gd)');
        $output->writeln('');

        $gdImage = $this->resizeImageGd($this->createGdImageFromGivenPath($imagePath), $width, $mode);

        $width = imagesx($gdImage);
        $height = imagesy($gdImage);

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

                $this->print1x2Pixel(
                    $output,
                    Color::convertIntToHex($colorTop),
                    $colorBottom === null ? self::NAME_TRANSPARENT : Color::convertIntToHex($colorBottom)
                );
            }
            $output->writeln('');
        }
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
     * @param ImageData $imageData
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function printImageData(ImageData $imageData, OutputInterface $output): void
    {
        $outputLines = $this->getOutputLines($imageData);
        $outputMaxLength = $this->getMaxLength($outputLines);

        $output->writeln('');
        $format = sprintf(self::REGEXP_OUTPUT, ImageData::WIDTH_TITLE, ImageData::WIDTH_TITLE, '%s');
        $output->writeln(sprintf($format, 'Title', 'Key', '', 'Value', ''));
        $output->writeln(str_repeat('-', $outputMaxLength));
        foreach ($outputLines as $outputLine) {
            $output->writeln($outputLine);
        }
        $output->writeln('');
    }

    /**
     * Prints 1x2 pixel.
     *
     * @param OutputInterface $output
     * @param string $colorTop
     * @param string|null $colorBottom
     * @return void
     * @throws Exception
     */
    protected function print1x2Pixel(OutputInterface $output, string $colorTop, ?string $colorBottom = null): void
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
                $output->write(' ');
                return;

            case $colorTop === self::NAME_TRANSPARENT:
                $rgb = Color::convertHexToRgb($colorTop);
                $output->write(sprintf("\x1b[38;2;%d;%d;%dm%s\x1b[0m", $rgb['r'], $rgb['g'], $rgb['b'], '▄'));
                return;

            case $colorBottom === self::NAME_TRANSPARENT:
                $rgb = Color::convertHexToRgb($colorTop);
                $output->write(sprintf("\x1b[38;2;%d;%d;%dm%s\x1b[0m", $rgb['r'], $rgb['g'], $rgb['b'], '▀'));
                return;

            default:
                $rgbTop = Color::convertHexToRgb($colorTop);
                $rgbBottom = Color::convertHexToRgb($colorBottom);
                $output->write(sprintf(
                    "\x1b[38;2;%d;%d;%dm\x1b[48;2;%d;%d;%dm%s\x1b[0m",
                    $rgbTop['r'],
                    $rgbTop['g'],
                    $rgbTop['b'],
                    $rgbBottom['r'],
                    $rgbBottom['g'],
                    $rgbBottom['b'],
                    '▀'
                ));
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
        /* Read parameter. */
        $imagePath = strval($input->getArgument('path'));

        if (!file_exists($imagePath)) {
            $output->writeln(sprintf('Unable to find image file "%s".', $imagePath));
            return Command::INVALID;
        }

        /* Print image */
        $this->printImageGd($imagePath, $output);

        /* Print image data. */
        $this->printImageData(new ImageData($imagePath), $output);

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
