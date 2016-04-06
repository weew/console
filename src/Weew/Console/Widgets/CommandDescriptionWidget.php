<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class CommandDescriptionWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * CommandDescriptionWidget constructor.
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
        $description = $command->getDescription();

        if ($description) {
            $this->output->writeLine("<keyword>$description</keyword>");
        }
    }
}
