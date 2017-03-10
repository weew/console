<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;

interface ICommandExecutionLock {
    /**
     * @param ICommand $command
     * @param bool $allowParallelism
     *
     * @return string
     */
    function lockCommand(ICommand $command, $allowParallelism = true);

    /**
     * @param ICommand $command
     */
    function unlockCommand(ICommand $command);

    /**
     * Delete all locks created by this particular instance.
     */
    function unlockAllCommands();
}
