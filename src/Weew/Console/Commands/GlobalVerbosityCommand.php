<?php

namespace Weew\Console\Commands;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputVerbosity;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalVerbosityCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true)->setHidden(true);
        $command->argument(ArgumentType::MULTIPLE_OPTIONAL, 'args');
        $command->option(OptionType::INCREMENTAL, '--verbosity', '-v')
            ->setDescription('Output verbosity: 0 = normal, 1 = verbose, 2 = debug, -1 = silent');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     */
    public function run(IInput $input, IOutput $output) {
        if ($input->hasOption('--verbosity')) {
            $verbosity = OutputVerbosity::getVerbosityForLevel(
                $input->getOption('--verbosity', 0)
            );
            $output->setOutputVerbosity($verbosity);
        }
    }
}
