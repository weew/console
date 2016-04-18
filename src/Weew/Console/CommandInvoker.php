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
     *
     * @return mixed
     */
    public function setup($handler, ICommand $command) {
        return $handler->setup($command);
    }

    /**
     * @param object $handler
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     *
     * @return mixed
     */
    public function run($handler, IInput $input, IOutput $output, IConsole $console) {
        return $handler->run($input, $output, $console);
    }
}
