<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Weew\Console\CommandExecutionLock;
use Weew\Console\Exceptions\CommandIsAlreadyRunningException;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandExecutionLock
 */
class CommandExecutionLockSpec extends ObjectBehavior {
    function getTestLockFile() {
        return __DIR__ . '/lock_file';
    }

    function letgo() {
        $this->unlockAllCommands();
    }

    function it_is_initializable() {
        $this->shouldHaveType(CommandExecutionLock::class);
    }

    function it_locks_command() {
        $command = new Command('name');
        $command->setParallel(false);
        $lockFile = $this->lockCommand($command)->getWrappedObject();
        it(file_exists($lockFile))->shouldBe(true);
    }

    function it_unlocks_command() {
        $command = new Command('name');
        $command->setParallel(false);
        $lockFile = $this->lockCommand($command)->getWrappedObject();
        it(file_exists($lockFile))->shouldBe(true);
        $this->unlockCommand($command);
        it(file_exists($lockFile))->shouldBe(false);
    }

    function it_unlocks_all_commands() {
        $command1 = new Command('name1');
        $command1->setParallel(false);
        $command2 = new Command('name2');
        $command2->setParallel(false);

        $lockFile1 = $this->lockCommand($command1)->getWrappedObject();
        $lockFile2 = $this->lockCommand($command2)->getWrappedObject();

        it(file_exists($lockFile1))->shouldBe(true);
        it(file_exists($lockFile2))->shouldBe(true);

        $this->unlockAllCommands();

        it(file_exists($lockFile1))->shouldBe(false);
        it(file_exists($lockFile2))->shouldBe(false);
    }

    function it_throws_an_error_if_command_is_already_locked() {
        $command = new Command('name');
        $command->setParallel(false);
        $this->lockCommand($command)->getWrappedObject();
        $this->shouldThrow(CommandIsAlreadyRunningException::class)
            ->during('lockCommand', [$command]);
    }

    function it_locks_command_if_parallelism_is_not_allowed() {
        $command = new Command('name');
        $command->setParallel(true);
        $lockFile = $this->lockCommand($command, false)->getWrappedObject();
        it(file_exists($lockFile))->shouldBe(true);
    }

    function it_does_not_lock_command_if_parallelism_is_allowed() {
        $command = new Command('name');
        $command->setParallel(true);
        $this->lockCommand($command, true)->shouldBe(null);
        $this->lockCommand($command, true)->shouldBe(null);
    }
}
