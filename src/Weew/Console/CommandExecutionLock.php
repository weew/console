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
     * CommandExecutionLock constructor.
     *
     * @param string $lockFile
     */
    public function __construct($lockFile = null) {
        if ($lockFile === null) {
            $lockFile = $this->getDefaultLockFile();
        }

        $this->setLockFile($lockFile);
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
        $data = $this->readLockFile();
        $data[$value] = (new DateTime())->format(DateTime::ATOM);
        $this->writeLockFile($data);
    }

    /**
     * @param string $value
     */
    public function removeFromLockFile($value) {
        $data = $this->readLockFile();
        array_remove($data, $value);
        $this->writeLockFile($data);
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

        $self = $this;
        register_shutdown_function(function() use ($self, $command) {
            $self->removeFromLockFile($command->getName());
        }, [$this, $command]);
    }

    /**
     * @return string
     */
    protected function getDefaultLockFile() {
        return path(sys_get_temp_dir(), md5(__DIR__) . '_console_lock');
    }
}
