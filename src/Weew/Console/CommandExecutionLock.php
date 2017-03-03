<?php

namespace Weew\Console;

use DateTime;
use Exception;
use Weew\Console\Exceptions\CommandIsAlreadyRunningException;
use Weew\ConsoleArguments\ICommand;
use Weew\JsonEncoder\JsonEncoder;

class CommandExecutionLock implements ICommandExecutionLock {
    /**
     * @var string
     */
    protected $lockFile;

    /**
     * @var array
     */
    protected $recentCommands = [];

    /**
     * CommandExecutionLock constructor.
     *
     * @param string $lockFile
     */
    public function __construct($lockFile = null) {
        if ($lockFile === null) {
            $lockFile = $this->getDefaultLockFile();
        }

        $this->setLockFile($lockFile);
        $this->handleShutdowns();
    }

    /**
     * @return string
     */
    public function getLockFile() {
        return $this->lockFile;
    }

    /**
     * @param string $lockFile
     */
    public function setLockFile($lockFile) {
        $this->lockFile = $lockFile;
    }

    /**
     * @return array
     */
    public function readLockFile() {
        $lockFile = $this->getLockFile();
        $data = [];

        if (file_exists($lockFile)) {
            try {
                $encoder = new JsonEncoder();
                $data = $encoder->decode(file_read($lockFile));

                if ($data === null) {
                    $data = [];
                }
            } catch (Exception $ex) {}
        }

        return $data;
    }

    /**
     * @param array $data
     */
    public function writeLockFile(array $data) {
        $lockFile = $this->getLockFile();
        $encoder = new JsonEncoder();

        file_write($lockFile, $encoder->encode($data));
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isInLockFile($value) {
        return array_has($this->readLockFile(), $value);
    }

    /**
     * @param string $value
     */
    public function addToLockFile($value) {
        // use key -> value here for easier lookup
        $this->recentCommands[$value] = true;

        $data = $this->readLockFile();
        $data[$value] = (new DateTime())->format(DateTime::ATOM);
        $this->writeLockFile($data);
    }

    /**
     * @param string $value
     */
    public function removeFromLockFile($value) {
        unset($this->recentCommands[$value]);

        $data = $this->readLockFile();
        array_remove($data, $value);
        $this->writeLockFile($data);
    }

    /**
     * Remove all locks for commands called trough
     * this particular lock instance.
     */
    public function removeRecentCommandsFromLockFile() {
        foreach ($this->recentCommands as $commandName => $status) {
            $this->removeFromLockFile($commandName);
        }
    }

    /**
     * @param IConsole $console
     * @param ICommand $command
     */
    public function lockCommand(IConsole $console, ICommand $command) {
        if ($command->isParallel() && $console->getAllowParallel()) {
            return;
        }

        if ($this->isInLockFile($command->getName())) {
            throw new CommandIsAlreadyRunningException(s(
                'Command "%s" is already being executed. ' .
                'Parallel execution for this command has been forbidden. ' .
                'This is the corresponding lock file "%s".',
                $command->getName(),
                $this->getLockFile()
            ));
        }

        $this->addToLockFile($command->getName());
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

            $self->removeRecentCommandsFromLockFile();
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
    protected function getDefaultLockFile() {
        return path(sys_get_temp_dir(), md5(__DIR__), '_console_lock');
    }
}
