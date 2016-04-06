<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;

class GlobalOptionsWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * GlobalOptionsWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Render options.
     */
    public function render() {
        $table = new TableWidget($this->input, $this->output);
        $table
            ->setTitle("<header>Global options:</header>")
            ->addRow("<keyword>-s, --silent</keyword>", "Disable output")
            ->addRow("<keyword>-n, --no-interaction</keyword>", "Disable interactions")
            ->addRow("<keyword>-V, --version</keyword>", "Show application version")
            ->addRow("<keyword>-h, --help</keyword>", "Show help text")
            ->addRow("<keyword>-f, --format</keyword>", "Output format: normal, plain, raw")
            ->addRow("<keyword>-v|vv|vvv, --verbosity</keyword>", "Output verbosity: 0 = normal, 1 = verbose, 2 = debug, -1 = silent");

        $table->render();
    }
}
