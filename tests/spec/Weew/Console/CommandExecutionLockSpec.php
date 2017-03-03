<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Weew\Console\CommandExecutionLock;
use Weew\Console\Console;
use Weew\Console\Exceptions\CommandIsAlreadyRunningException;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandExecutionLock
 */
class CommandExecutionLockSpec extends ObjectBehavior {
    function getTestLockFile() {
        return __DIR__ . '/lock_file';
    }

    function let() {
        $lockFile = $this->getTestLockFile();
        $this->beConstructedWith($lockFile);

        file_delete($lockFile);
        register_shutdown_function(function() use ($lockFile) {
            file_delete($lockFile);
        });
    }

    function it_is_initializable() {
        $this->shouldHaveType(CommandExecutionLock::class);
    }

    function it_falls_back_to_a_default_lock_file() {
        $this->beConstructedWith();
        $this->getLockFile()->shouldBeString();
    }

    function it_returns_lock_file() {
        $this->beConstructedWith('lock1');
        $this->getLockFile()->shouldBe('lock1');
        $this->setLockFile('lock2');
        $this->getLockFile()->shouldBe('lock2');
    }

    function it_reads_json_from_lock_file() {
        file_write($this->getTestLockFile(), json_encode(['data']));
        $this->readLockFile()->shouldBe(['data']);
    }

    function it_returns_an_empty_array_if_lock_file_does_not_exist() {
        $this->readLockFile()->shouldBe([]);
    }

    function it_returns_an_empty_array_if_lock_file_content_is_not_valid_json() {
        file_write($this->getTestLockFile(), 'data');
        $this->readLockFile()->shouldBe([]);
    }

    function it_returns_an_empty_array_if_lock_file_json_results_in_null() {
        file_write($this->getTestLockFile(), '');
        $this->readLockFile()->shouldBe([]);
    }

    function it_writes_to_lock_file() {
        $this->writeLockFile(['data']);
        $this->readLockFile()->shouldBe(['data']);
    }

    function it_can_tell_if_a_value_is_inside_lock_file() {
        file_write($this->getTestLockFile(), json_encode(['value1' => true]));
        $this->isInLockFile('value1')->shouldBe(true);
        $this->isInLockFile('value2')->shouldBe(false);
    }

    function it_adds_to_lock_file() {
        $this->addToLockFile('value1');
        $this->addToLockFile('value2');
        $this->isInLockFile('value1')->shouldBe(true);
        $this->isInLockFile('value2')->shouldBe(true);
        $this->isInLockFile('value3')->shouldBe(false);
    }

    function it_removes_from_lock_file() {
        $this->addToLockFile('value1');
        $this->addToLockFile('value2');
        $this->removeFromLockFile('value1');
        $this->isInLockFile('value1')->shouldBe(false);
        $this->isInLockFile('value2')->shouldBe(true);
    }

    function it_locks_command() {
        $console = new Console();
        $command = new Command('name');
        $command->setParallel(false);
        $this->lockCommand($console, $command);
        $this->isInLockFile('name')->shouldBe(true);
    }

    function it_locks_a_parallel_command_if_console_disallows_parallelism() {
        $console = new Console();
        $console->setAllowParallel(false);
        $command = new Command('name');
        $this->lockCommand($console, $command);
        $this->isInLockFile('name')->shouldBe(true);
    }

    function it_locks_commands_an_throws_an_error_if_command_is_already_locked() {
        $console = new Console();
        $command = new Command('name');
        $command->setParallel(false);
        $this->lockCommand($console, $command);
        $this->isInLockFile('name')->shouldBe(true);

        $this->shouldThrow(CommandIsAlreadyRunningException::class)
            ->during('lockCommand', [$console, $command]);
    }

    function it_does_not_lock_parallel_commands() {
        $console = new Console();
        $command = new Command('name');
        $this->lockCommand($console, $command);
        $this->isInLockFile('name')->shouldBe(false);
    }

    function it_removes_all_recent_commands_from_lock() {
        $console = new Console();
        $console->setAllowParallel(false);
        $this->lockCommand($console, new Command('name1'));
        $this->lockCommand($console, new Command('name2'));

        $this->isInLockFile('name1')->shouldBe(true);
        $this->isInLockFile('name2')->shouldBe(true);

        $this->removeRecentCommandsFromLockFile();

        $this->isInLockFile('name1')->shouldBe(false);
        $this->isInLockFile('name2')->shouldBe(false);
    }
}
