<?php

namespace Weew\Console\Commands;

use Weew\Console\IInput;
use Weew\Console\InputVerbosity;
use Weew\Console\IOutput;
use Weew\Console\OutputVerbosity;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalSilentModeCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true)->setHidden(true);
        $command->option(OptionType::BOOLEAN, '--silent', '-s')
            ->setDescription('Disable input and output');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     */
    public function run(IInput $input, IOutput $output) {
        if ($input->getOption('--silent')) {
            $input->setInputVerbosity(InputVerbosity::SILENT);
            $output->setOutputVerbosity(OutputVerbosity::SILENT);
        }
    }
}
