<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;

interface ICommandInvoker {
    /**
     * @param string $handler
     *
     * @return object
     */
    function create($handler);

    /**
     * @param object $handler
     * @param ICommand $command
     */
    function setup($handler, ICommand $command);

    /**
     * @param object $handler
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     */
    function run($handler, IInput $input, IOutput $output, IConsole $console);
}
