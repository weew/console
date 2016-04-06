<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;

class CommandInvoker implements ICommandInvoker {
    /**
     * @param string $handler
     *
     * @return object
     */
    public function create($handler) {
        return new $handler();
    }

    /**
     * @param object $handler
     * @param ICommand $command
     */
    public function setup($handler, ICommand $command) {
        $handler->setup($command);
    }

    /**
     * @param object $handler
     * @param IInput $input
     * @param IOutput $output
     */
    public function run($handler, IInput $input, IOutput $output) {
        $handler->run($input, $output);
    }
}
