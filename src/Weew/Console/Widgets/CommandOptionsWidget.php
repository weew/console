<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class CommandOptionsWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * CommandOptionsWidget constructor.
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
        $options = $command->getOptions();

        if (count($options) === 0) {
            return;
        }

        $table = new TableWidget($this->input, $this->output);
        $table->setTitle("<header>Options:</header>");

        foreach ($options as $option) {
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

        $this->output->writeLine();
        $table->render();
    }
}
