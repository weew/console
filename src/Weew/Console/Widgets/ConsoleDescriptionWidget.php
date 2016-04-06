<?php

namespace Weew\Console\Widgets;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;

class ConsoleDescriptionWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * ConsoleDescriptionWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param IConsole $console
     */
    public function render(IConsole $console) {
        $title = $console->getTitle();
        $description = $console->getDescription();

        if ($title === null) {
            $title = 'Console application';
        }

        if ($description === null) {
            $description = 'A unified console for all kinds of commands';
        }

        $this->output->writeLine("<keyword>$title</keyword>");
        $this->output->writeLine($description);
    }
}
