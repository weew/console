<?php

namespace Weew\Console;

use Weew\Console\Exceptions\CommandIsAlreadyRunningException;
use Weew\ConsoleArguments\ICommand;

class CommandExecutionLock implements ICommandExecutionLock {
    /**
     * @var array
     */
    protected $locks = [];

    /**
     * CommandExecutionLock constructor.
     */
    public function __construct() {
        $this->handleShutdowns();
    }

    /**
     * @param ICommand $command
     * @param bool $allowParallelism
     *
     * @return string
     */
    public function lockCommand(ICommand $command, $allowParallelism = true) {
        if ($command->isParallel() && $allowParallelism) {
            return;
        }

        if ($this->isLocked($command->getName())) {
            throw new CommandIsAlreadyRunningException(s(
                'Command "%s" is already being executed. ' .
                'Parallel execution for this command has been forbidden. ' .
                'This is the corresponding lock file "%s".',
                $command->getName(),
                $this->getLockName($command->getName())
            ));
        }

        return $this->createLock($command->getName());
    }

    /**
     * @param ICommand $command
     */
    public function unlockCommand(ICommand $command) {
        $this->deleteLock($command->getName());
    }

    /**
     * Delete all locks created by this particular instance.
     */
    public function unlockAllCommands() {
        $this->deleteAllLocks();
    }

    /**
     * Handle shutdown events and clean up lock files.
     */
    protected function handleShutdowns() {
        declare(ticks = 1);

        $self = $this;

        $cleanup = function($signal = null) use ($self) {
            if ($signal === SIGTERM) {
                fprintf(STDERR, 'Received SIGTERM...');
            } else if ($signal === SIGINT) {
                fprintf(STDERR, 'Received SIGINT...');
            } else if ($signal === SIGTSTP) {
                fprintf(STDERR, 'Received SIGTSTP...');
            }

            $self->deleteAllLocks();
            exit;
        };

        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGTERM, $cleanup, false);
            pcntl_signal(SIGINT, $cleanup, false);
            pcntl_signal(SIGTSTP, $cleanup, false);
        }

        register_shutdown_function($cleanup);
    }

    /**
     * @return string
     */
    protected function getLockFileBaseName() {
        return path(sys_get_temp_dir(), md5(__DIR__), 'console_lock');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function getLockName($value) {
        return s('%s_%s', $this->getLockFileBaseName(), md5($value));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function createLock($value) {
        $lockFile = $this->getLockName($value);
        file_create($lockFile);
        $this->locks[$value] = $lockFile;

        return $lockFile;
    }

    /**
     * @param string $value
     */
    protected function deleteLock($value) {
        file_delete($this->getLockName($value));
        unset($this->locks[$value]);
    }

    /**
     * Remove all locks for commands called trough
     * this particular lock instance.
     */
    protected function deleteAllLocks() {
        foreach ($this->locks as $commandName => $lockFile) {
            $this->deleteLock($commandName);
        }
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isLocked($value) {
        return file_exists($this->getLockName($value));
    }
}
