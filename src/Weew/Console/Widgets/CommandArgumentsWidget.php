<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class CommandArgumentsWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * CommandArgumentsWidget constructor.
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
        $arguments = $command->getArguments();

        if (count($arguments) === 0) {
            return;
        }

        $table = new TableWidget($this->input, $this->output);
        $table->setTitle("<header>Arguments:</header>");

        foreach ($arguments as $argument) {
            $name = $argument->getName();
            $description = $argument->getDescription();

            if ($argument->isRequired()) {
                if ($argument->isMultiple()) {
                    $name = "\<$name...>";
                } else {
                    $name = "\<name>";
                }
            } else {
                if ($argument->isMultiple()) {
                    $name = "\<$name... ?>";
                } else {
                    $name = "\<name ?>";
                }
            }

            $table->addRow("<keyword>$name</keyword>", $description);
        }

        $table->render();
    }
}
