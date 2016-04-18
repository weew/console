<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalHelpCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true);
        $command->argument(ArgumentType::MULTIPLE_OPTIONAL, 'args');
        $command->option(OptionType::BOOLEAN, '--help', '-h')
            ->setDescription('Show help text');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     *
     * @return bool
     */
    public function run(IInput $input, IOutput $output, IConsole $console) {
        if ($input->hasOption('--help')) {
            $args = $input->getArgument('args');
            $subject = array_pop($args);

            $console->parseString("help $subject");

            return false;
        }
    }
}
