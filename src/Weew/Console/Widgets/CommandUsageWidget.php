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

        $usage = "$name ";

        if (count($command->getArguments())) {
            $usage .= $this->getArgumentsUsage($command->getArguments());
        }

        if (count($command->getOptions())) {
            $usage .= ' ' . $this->getOptionsUsage($command->getOptions());
        }

        $usage = trim($usage);


        $this->output->writeLine();
        $this->output->writeLine("<header>Usage:</header>");
        $this->output->writeLineIndented("$usage");
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    protected function getArgumentsUsage(array $arguments) {
        $usage = '';

        foreach ($arguments as $argument) {
            $name = $argument->getName();
            $info = '';

            if ($argument->isRequired()) {
                if ($argument->isMultiple()) {
                    $info .= "$name...";
                } else {
                    $info .= "$name";
                }
            } else {
                if ($argument->isMultiple()) {
                    $info .= "$name... ?";
                } else {
                    $info .= "$name ?";
                }
            }

            $usage .= " \<<green>$info</green>>";
        }

        return $usage;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function getOptionsUsage(array $options) {
        $usage = '';

        foreach ($options as $option) {
            $name = $option->getName();
            $alias = $option->getAlias();
            $names = [];
            $info = '';

            if ($name) {
                $names[] = $name;
            }

            if ($alias) {
                $names[] = $alias;
            }

            $names = implode('|', $names);

            if ($option->isRequired()) {
                if ($option->isMultiple()) {
                    $info .= "$names...";
                } else {
                    $info .= "$names";
                }
            } else {
                if ($option->isMultiple()) {
                    $info .= "$names... ?";
                } else {
                    $info .= "$names ?";
                }
            }

            if ($option->isIncremental()) {
                $info .= '=1..n';
            } else if ($option->isBoolean()) {
                $info .= '=true/false';
            }

            $usage .= "[<green>$info</green>]";
        }

        return $usage;
    }
}
