<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\Widgets\AvailableCommandsWidget;
use Weew\Console\Widgets\ConsoleDescriptionWidget;
use Weew\Console\Widgets\GlobalOptionsWidget;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\ICommand;

class ListCommand {
    /**
     * @var IConsole
     */
    protected $console;

    /**
     * DefaultCommandHandler constructor.
     *
     * @param IConsole $console
     */
    public function __construct(IConsole $console) {
        $this->console = $console;
    }

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
     */
    public function run(IInput $input, IOutput $output) {
        $widget = new ConsoleDescriptionWidget($input, $output);
        $widget->render($this->console);

        $widget = new GlobalOptionsWidget($input, $output);
        $widget->render();

        $widget = new AvailableCommandsWidget($input, $output);
        $widget->render($this->console);
    }
}
