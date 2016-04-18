<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\Widgets\AvailableCommandsWidget;
use Weew\Console\Widgets\ConsoleDescriptionWidget;
use Weew\Console\Widgets\GlobalOptionsWidget;
use Weew\ConsoleArguments\ICommand;

class ListCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command
            ->setName('list')
            ->setDescription('List all available commands');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     */
    public function run(IInput $input, IOutput $output, IConsole $console) {
        $widget = new ConsoleDescriptionWidget($input, $output);
        $widget->render($console);

        $widget = new GlobalOptionsWidget($input, $output, $console);
        $widget->render();

        $widget = new AvailableCommandsWidget($input, $output);
        $widget->render($console);
    }
}
