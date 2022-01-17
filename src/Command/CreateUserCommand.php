<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2022 Björn Hempel <bjoern@hempel.li>
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

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class CreateUserCommand
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-16)
 * @package App\Command
 * @example bin/console app:user:create "email@domain.tld" "password" "firstname" "lastname"
 */
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    private EntityManagerInterface $manager;

    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this
            ->setName('app:user:create')
            ->setDescription('Creates a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('firstName', InputArgument::OPTIONAL, 'The first name'),
                new InputArgument('lastName', InputArgument::OPTIONAL, 'The last name'),
            ])
            ->setHelp(
                <<<'EOT'

The <info>app:user:create</info> command creates a user:
  <info>php %command.full_name%</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the first and second arguments:
  <info>php %command.full_name% email@domain.tld password</info>

You can even set the first and last name which are not required by specifying the first and last name as the third and fourth arguments:
  <info>php %command.full_name% email@domain.tld password firstname lastname</info>

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
        /* Read parameter. */
        $email = strval($input->getArgument('email'));
        $password = strval($input->getArgument('password'));
        $firstName = strval($input->getArgument('firstName'));
        $lastName = strval($input->getArgument('lastName'));

        /* Create a new user. */
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setIdHash(sha1(rand(1000000, 9999999).rand(1000000, 9999999)));

        /* Persists the user. */
        $this->manager->persist($user);
        $this->manager->flush();

        /* Print information for user. */
        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));

        /* Command successfully executed. */
        return Command::SUCCESS;
    }

    /**
     * Interaction with the user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        /* Ask user for email. */
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new Exception('Email can not be empty');
                }
                return $email;
            });
            $questions['email'] = $question;
        }

        /* Ask user for password. */
        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new Exception('Password can not be empty');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        /* Create QuestionHelper instance. */
        $questionHelper = $this->getHelper('question');

        /* Check QuestionHelper instance. */
        if (!$questionHelper instanceof QuestionHelper) {
            throw new Exception(sprintf('Unexpected class (%s:%d)', __FILE__, __LINE__));
        }

        /* Go through questions. */
        foreach ($questions as $name => $question) {
            $answer = $questionHelper->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
