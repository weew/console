<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use tests\spec\Weew\Console\Mocks\ErrorCommand;
use tests\spec\Weew\Console\Mocks\FakeCommand;
use Weew\Console\CommandExecutionLock;
use Weew\Console\CommandInvoker;
use Weew\Console\Console;
use Weew\Console\Exceptions\InvalidCommandException;
use Weew\Console\ICommandExecutionLock;
use Weew\Console\ICommandInvoker;
use Weew\Console\IInput;
use Weew\Console\Input;
use Weew\Console\IOutput;
use Weew\Console\Output;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleFormatter\ConsoleFormatter;
use Weew\ConsoleFormatter\IConsoleFormatter;

/**
 * @mixin Console
 */
class ConsoleSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(Console::class);
    }

    function it_takes_and_returns_title() {
        $this->getTitle()->shouldBe(null);
        $this->setTitle('title');
        $this->getTitle()->shouldBe('title');
    }

    function it_is_chainable_trough_set_title() {
        $this->setTitle('title')->shouldBe($this);
    }

    function it_takes_and_returns_description() {
        $this->getDescription()->shouldBe(null);
        $this->setDescription('description');
        $this->getDescription()->shouldBe('description');
    }

    function it_is_chainable_trough_set_description() {
        $this->setDescription('description')->shouldBe($this);
    }

    function it_takes_and_returns_version() {
        $this->getVersion()->shouldBe('1.0');
        $this->setVersion('version');
        $this->getVersion()->shouldBe('version');
    }

    function it_takes_and_returns_allow_parallel() {
        $this->getAllowParallel()->shouldBe(true);
        $this->setAllowParallel(false);
        $this->getAllowParallel()->shouldBe(false);
    }

    function it_is_chainable_trough_set_allow_parallel() {
        $this->setAllowParallel(true)->shouldBe($this);
    }

    function it_is_chainable_trough_set_version() {
        $this->setVersion('version')->shouldBe($this);
    }

    function it_takes_and_returns_commands() {
        $this->setCommands([new FakeCommand()]);
        $this->getCommands()->shouldHaveCount(1);
    }

    function it_adds_commands() {
        $this->setCommands([new FakeCommand()]);
        $this->addCommands([new FakeCommand()]);
        $this->getCommands()->shouldHaveCount(2);
    }

    function it_adds_command() {
        $this->setCommands([new FakeCommand()]);
        $this->addCommand(new FakeCommand());
        $this->getCommands()->shouldHaveCount(2);
    }

    function it_instantiates_command() {
        $this->setCommands([FakeCommand::class]);
        $this->getCommands()->shouldHaveCount(1);
        $this->getCommands()[0]->getHandler()->shouldHaveType(FakeCommand::class);
    }

    function it_accepts_plain_commands() {
        $command = new Command();
        $command->setHandler(FakeCommand::class);
        $this->setCommands([$command]);
        $this->getCommands()->shouldHaveCount(1);
        $this->getCommands()[0]->getHandler()->shouldHaveType(FakeCommand::class);
    }

    function it_throws_an_error_if_command_has_no_valid_methods() {
        $this->shouldThrow(InvalidCommandException::class)
            ->during('addCommand', [stdClass::class]);
    }

    function it_throws_an_error_if_command_is_either_an_object_nor_string() {
        $this->shouldThrow(InvalidCommandException::class)
            ->during('addCommand', [[]]);
    }

    function it_throws_an_error_if_command_class_does_not_exist() {
        $this->shouldThrow(InvalidCommandException::class)
            ->during('addCommand', ['some_class']);
    }

    function it_setups_command(FakeCommand $command) {
        $command->setup(Argument::type(ICommand::class))->shouldBeCalled();
        $this->addCommand($command);
    }

    function it_takes_and_returns_console_formatter() {
        $formatter = new ConsoleFormatter();
        $this->getConsoleFormatter()->shouldHaveType(IConsoleFormatter::class);
        $this->setConsoleFormatter($formatter);
        $this->getConsoleFormatter()->shouldBe($formatter);
    }

    function it_takes_and_returns_output() {
        $output = new Output();
        $this->getOutput()->shouldHaveType(IOutput::class);
        $this->setOutput($output);
        $this->getOutput()->shouldBe($output);
    }

    function it_takes_and_returns_input() {
        $output = new Input();
        $this->getInput()->shouldHaveType(IInput::class);
        $this->setInput($output);
        $this->getInput()->shouldBe($output);
    }

    function it_takes_and_returns_command_invoker() {
        $commandInvoker = new CommandInvoker();
        $this->getCommandInvoker()->shouldHaveType(ICommandInvoker::class);
        $this->setCommandInvoker($commandInvoker);
        $this->getCommandInvoker()->shouldBe($commandInvoker);
    }

    function it_takes_and_returns_command_execution_lock() {
        $commandExecutionLock = new CommandExecutionLock();
        $this->getCommandExecutionLock()->shouldHaveType(ICommandExecutionLock::class);
        $this->setCommandExecutionLock($commandExecutionLock);
        $this->getCommandExecutionLock()->shouldBe($commandExecutionLock);
    }

    function it_parses_args_as_string() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('');
    }

    function it_parses_args_as_array() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseArgs([]);
    }

    function it_parses_args_as_argv() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseArgv([]);
        $this->parseArgv();
    }

    function it_handles_unknown_commands(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run()->shouldNotBeCalled();
        $this->parseString('command');
    }

    function it_handles_command_errors() {
        $command = new ErrorCommand();
        $consoleCommand = new Command('error');
        $consoleCommand->setHandler($command);
        $this->addCommand($consoleCommand);

        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('error');
    }

    function it_takes_and_returns_default_command_name() {
        $this->getDefaultCommandName()->shouldBe('list');
        $this->setDefaultCommandName('name');
        $this->getDefaultCommandName()->shouldBe('name');
    }

    function it_runs_and_interrupts_for_global_command() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('list --help');
    }

    function it_takes_and_returns_catch_errors_flag() {
        $this->getCatchErrors()->shouldBe(true);
        $this->setCatchErrors(false)->shouldBe($this);
        $this->getCatchErrors()->shouldBe(false);
    }
}
