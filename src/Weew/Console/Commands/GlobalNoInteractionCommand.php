<?php

namespace Weew\Console\Commands;

use Weew\Console\IInput;
use Weew\Console\InputVerbosity;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalNoInteractionCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true)->setHidden(true);
        $command->option(OptionType::BOOLEAN, '--no-interaction', '-n')
            ->setDescription('Disable interactions like questions, prompts, etc');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     */
    public function run(IInput $input, IOutput $output) {
        if ($input->getOption('--no-interaction')) {
            $input->setInputVerbosity(InputVerbosity::SILENT);
        }
    }
}
