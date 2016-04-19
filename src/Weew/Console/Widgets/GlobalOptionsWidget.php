<?php

namespace Weew\Console\Widgets;

use Weew\Console\IConsole;
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
     * @var IConsole
     */
    private $console;

    /**
     * GlobalOptionsWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     */
    public function __construct(IInput $input, IOutput $output, IConsole $console) {
        $this->input = $input;
        $this->output = $output;
        $this->console = $console;
    }

    /**
     * Render options.
     */
    public function render() {
        $table = new TableWidget($this->input, $this->output);
        $table->setTitle("<header>Global options:</header>");

        foreach ($this->console->getCommands() as $command) {
            if ($command->isGlobal()) {
                foreach ($command->getOptions() as $option) {
                    $name = $option->getName();
                    $alias = '   ';

                    if ($option->getAlias()) {
                        $alias = $option->getAlias();

                        if ($option->isIncremental()) {
                            $alias = s('-:a|:a:a|:a:a:a', [':a' => substr($alias, 1)]);
                        }

                        $alias .= ',';
                    }

                    $table->addRow("<keyword>$alias $name</keyword>", $option->getDescription());
                }
            }
        }

        $this->output->writeLine();
        $table->render();
    }
}
