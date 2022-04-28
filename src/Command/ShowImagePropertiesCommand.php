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
 */
class ShowImagePropertiesCommand extends Command
{
    protected static $defaultName = 'app:image:show';

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
            throw new Exception(sprintf('Unable to find image file "%s" (%s:%d).', $imagePath, __FILE__, __LINE__));
        }

        $imageExif = new ImageData($imagePath);

        $dataImage = $imageExif->getDataImage();

        $output->writeln('');
        $format = sprintf('%%-%ds %%-%ds %s', ImageData::WIDTH_TITLE, ImageData::WIDTH_TITLE, '%s');
        $output->writeln(sprintf($format, 'Title', 'Key', 'Value'));
        $output->writeln(str_repeat('-', 150));
        foreach ($dataImage as $key => $data) {
            $value = $data[ImageData::KEY_NAME_VALUE];

            if (!is_bool($value) && !is_float($value) && !is_int($value) && !is_string($value) && !is_null($value)) {
                throw new Exception(sprintf('Unsupported type "%s" given (%s:%d).', gettype($value), __FILE__, __LINE__));
            }

            $format = sprintf('%%-%ds %%-%ds %%s%s%%s', ImageData::WIDTH_TITLE, ImageData::WIDTH_TITLE, strval($data[ImageData::KEY_NAME_FORMAT]));
            $output->writeln(sprintf($format, strval($data[ImageData::KEY_NAME_TITLE]), $key, strval($data[ImageData::KEY_NAME_UNIT_BEFORE]), $value, strval($data[ImageData::KEY_NAME_UNIT])));
        }
        $output->writeln('');

        /* Command successfully executed. */
        return Command::SUCCESS;
    }
}
