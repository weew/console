<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalPassthroughCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true)->setHidden(true);
        $command->option(OptionType::BOOLEAN, '--passthrough')
            ->setDescription('Do not catch any errors or exceptions');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     *
     * @return bool
     */
    public function run(IInput $input, IOutput $output, IConsole $console) {
        if ($input->getOption('--passthrough')) {
            $console->setCatchErrors(false);
        }
    }
}
