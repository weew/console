<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class CommandUsageWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * CommandUsageWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param ICommand $command
     */
    public function render(ICommand $command) {
        $name = $command->getName();
        $usage = "$name";
        $arguments = $command->getArguments();

        if (count($arguments)) {
            foreach ($arguments as $argument) {
                $name = $argument->getName();

                if ($argument->isRequired()) {
                    if ($argument->isMultiple()) {
                        $usage .= " \<$name...>";
                    } else {
                        $usage .= " \<$name>";
                    }
                } else {
                    if ($argument->isMultiple()) {
                        $usage .= " \<$name... ?>";
                    } else {
                        $usage .= " \<$name ?>";
                    }
                }
            }
        }

        $this->output->writeLine();
        $this->output->writeLine("<header>Usage:</header>");
        $this->output->writeLineIndented("$usage");
    }
}
