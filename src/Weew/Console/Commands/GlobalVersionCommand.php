<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalVersionCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true);
        $command->option(OptionType::BOOLEAN, '--version', '-V')
            ->setDescription('Show application version');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     *
     * @return bool
     */
    public function run(IInput $input, IOutput $output, IConsole $console) {
        if ($input->getOption('--version')) {
            $version = $console->getVersion();

            $output->writeLine(
                "<keyword>Application version $version</keyword>"
            );

            return false;
        }
    }
}
