<?php

namespace Weew\Console\Widgets;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class AvailableCommandsWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * AvailableCommandsWidget constructor.
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
        $facts = $this->gatherFacts($console->getCommands());

        if (count($facts) > 0) {
            $table = new TableWidget($this->input, $this->output);
            $table->setTitle("<header>Available commands:</header>");

            $headers = [];

            foreach ($facts as $name => $description) {
                if (stripos($name, ':') !== false) {
                    $header = array_first(explode(':', $name));

                    if ( ! array_contains($headers, $header)) {
                        $headers[] = $header;

                        $table->addSection("<header>$header</header>");
                    }
                }

                $table->addRow("<keyword>$name</keyword>", $description);
            }

            $this->output->writeLine();
            $table->render();
        } else {
            $this->output->writeLineIndented('There are no commands yet');
        }
    }

    /**
     * @param ICommand[] $commands
     *
     * @return array
     */
    private function gatherFacts(array $commands) {
        $facts = [];

        foreach ($commands as $command) {
            if ( ! $command->isHidden() && ! $command->isGlobal()) {
                $facts[$command->getName()] = $command->getDescription();
            }
        }

        ksort($facts);

        $ungroupedFacts = [];
        $groupedFacts = [];

        foreach ($facts as $name => $description) {
            if (stripos($name, ':') === false) {
                $ungroupedFacts[$name] = $description;
            } else {
                $groupedFacts[$name] = $description;
            }
        }

        $facts = array_merge($ungroupedFacts, $groupedFacts);

        return $facts;
    }
}
