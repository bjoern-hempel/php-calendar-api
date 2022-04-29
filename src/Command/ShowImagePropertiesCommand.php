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

use App\Utils\ImageData;
use Exception;
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

    /* sudo apt install catimg */
    protected const IMAGE_VIEWER = 'catimg';

    protected const IMAGE_WIDTH = 180;

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
     * Prints image to screen.
     *
     * @param string $imagePath
     * @param OutputInterface $output
     * @return void
     */
    protected function printImage(string $imagePath, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('Image');
        $output->writeln('');

        if ($this->commandExist(self::IMAGE_VIEWER)) {
            $shellOutput = shell_exec(sprintf('%s -w %d %s', self::IMAGE_VIEWER, self::IMAGE_WIDTH, $imagePath));

            if ($shellOutput === null || $shellOutput === false) {
                $output->writeln(sprintf('Unable to execute %s command.', self::IMAGE_VIEWER));
            } else {
                $output->writeln($shellOutput);
            }
        } else {
            $output->writeln(sprintf('Application %s does not exists on your system.', self::IMAGE_VIEWER));
            $output->writeln(sprintf('You can install it via: sudo apt install %s', self::IMAGE_VIEWER));
        }
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
        $this->printImage($imagePath, $output);

        /* Print image data. */
        $this->printImageData(new ImageData($imagePath), $output);

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
