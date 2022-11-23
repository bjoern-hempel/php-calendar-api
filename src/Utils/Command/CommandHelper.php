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

namespace App\Utils\Command;

use App\Kernel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandHelper
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class CommandHelper
{
    protected const LINE_BREAK = "\n";

    protected const OUTPUT_WIDTH = 75;

    protected Application $application;

    protected bool $debug = false;

    /**
     * CommandHelper constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * Returns the application for this test.
     *
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return string|null
     */
    public function getEnvironment(): ?string
    {
        return $this->application->getKernel()->getEnvironment();
    }

    /**
     * @param string $environment
     * @param bool|null $debug
     * @return self
     */
    public function setEnvironment(string $environment, ?bool $debug = null): self
    {
        if ($debug !== null) {
            $this->setDebug($debug);
        }

        $kernel = new Kernel($environment, $this->isDebug());

        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Replaces and returns the configured commands.
     *
     * @param string[] $commands
     * @return string[]
     * @throws Exception
     */
    protected function translateCommands(array $commands): array
    {
        /* Gets the environment */
        $environment = $this->getEnvironment();

        $replaceElements = [
            '%(environment)s' => $environment,
        ];

        foreach ($commands as &$command) {
            $command = str_replace(
                array_keys($replaceElements),
                array_values($replaceElements),
                $command
            );
        }

        return $commands;
    }

    /**
     * Print and execute commands.
     *
     * @param string[] $command
     * @return void
     * @throws Exception
     */
    public function printAndExecuteCommands(array $command): void
    {
        /* translate the given command array. */
        $commands = $this->translateCommands($command);

        /* Print Header */
        print self::LINE_BREAK;
        print '┏━'.$this->strRepeatUntil('━', self::OUTPUT_WIDTH).'━┓'.self::LINE_BREAK;
        print '┃ '.$this->strRepeatUntil(' ', self::OUTPUT_WIDTH, sprintf('PREPARE THE DATABASE (%s)', $this->getEnvironment())).' ┃'.self::LINE_BREAK;
        print '┣━'.$this->strRepeatUntil('━', self::OUTPUT_WIDTH).'━┫'.self::LINE_BREAK;

        /* Execute commands */
        $number = 0;
        foreach ($commands as $comment => $command) {
            if ($number > 0) {
                print '┠─'.$this->strRepeatUntil('─', self::OUTPUT_WIDTH).'─┨'."\n";
            }

            print '┃ '.$this->strRepeatUntil(' ', self::OUTPUT_WIDTH, $comment).' ┃'.self::LINE_BREAK;
            print '┃ '.$this->strRepeatUntil(' ', self::OUTPUT_WIDTH, sprintf('$ bin/console %s', $command)).' ┃'.self::LINE_BREAK;

            $message = '~ Dry Run.';

            if (!$this->isDebug()) {
                $this->runCommand($command);
                $message = '~ Done.';
            }

            print '┃ '.$this->strRepeatUntil(' ', self::OUTPUT_WIDTH, $message).' ┃'.self::LINE_BREAK;

            $number++;
        }

        /* Print Footer */
        print '┗━'.$this->strRepeatUntil('━', self::OUTPUT_WIDTH).'━┛'."\n";
        print "\n";
    }

    /**
     * Print and execute commands.
     *
     * @param array<string, string> $command
     * @return array<int|string, mixed>
     * @throws Exception
     */
    public function returnAndExecuteCommands(array $command): array
    {
        $commands = $this->translateCommands($command);

        $data = [
            'header' => sprintf('Prepare the database (environment: %s)', $this->getEnvironment()),
            'commands' => [],
        ];

        /* Execute commands */
        $number = 0;
        foreach ($commands as $comment => $command) {
            $dataCommand = [
                'comment' => $comment,
                'command' => sprintf('$ bin/console %s', $command),
            ];

            $message = '~ Dry Run.';

            if (!$this->isDebug()) {
                $this->runCommand($command);
                $message = '~ Done.';
            }

            $dataCommand['status'] = $message;

            $data['commands'][] = $dataCommand;

            $number++;
        }

        return $data;
    }

    /**
     * Prints the given string and fill up with char to wanted length.
     *
     * @param string $char
     * @param int $length
     * @param string $alreadyIssued
     * @return string
     */
    public function strRepeatUntil(string $char, int $length, string $alreadyIssued = ''): string
    {
        return $alreadyIssued.str_repeat($char, $length - strlen($alreadyIssued));
    }

    /**
     * Runs the given command.
     *
     * @param string $command
     * @return int
     * @throws Exception
     */
    protected function runCommand(string $command): int
    {
        $command = sprintf('%s --quiet', $command);

        return $this->application->run(new StringInput($command));
    }
}
