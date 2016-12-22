<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;

interface ICommandExecutionLock {
    /**
     * @return string
     */
    function getLockFile();

    /**
     * @param string $lockFile
     */
    function setLockFile($lockFile);

    /**
     * @return array
     */
    function readLockFile();

    /**
     * @param array $data
     */
    function writeLockFile(array $data);

    /**
     * @param string $value
     *
     * @return bool
     */
    function isInLockFile($value);

    /**
     * @param string $value
     */
    function addToLockFile($value);

    /**
     * @param string $value
     */
    function removeFromLockFile($value);

    /**
     * @param IConsole $console
     * @param ICommand $command
     */
    function lockCommand(IConsole $console, ICommand $command);
}
