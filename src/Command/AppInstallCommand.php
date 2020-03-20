<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppInstallCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:install';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Create database and installs the application')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to install/reinstall fresh app!')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Drop database
        $command = $this->getApplication()->find('doctrine:database:drop');

        $arguments = [
            '--force'  => true,
            '--if-exists' => true
        ];

        $argsInput = new ArrayInput($arguments);
        $command->run($argsInput, $output);

        // Create Database

        $command = $this->getApplication()->find('doctrine:database:create');

        $argsInput = new ArrayInput([]);

        $command->run($argsInput, $output);

        // Run migrations

        $command = $this->getApplication()->find('doctrine:migrations:migrate');

        $argsInput = new ArrayInput([]);
        $argsInput->setInteractive(false);

        $command->run($argsInput, $output);

        // Load fixtures

        $command = $this->getApplication()->find('doctrine:fixtures:load');

        $argsInput = new ArrayInput([]);
        $argsInput->setInteractive(false);

        $command->run($argsInput, $output);

        return 0;
    }
}
