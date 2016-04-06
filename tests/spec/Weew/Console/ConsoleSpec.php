<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use tests\spec\Weew\Console\Mocks\ErrorCommand;
use tests\spec\Weew\Console\Mocks\FakeCommand;
use Weew\Console\CommandInvoker;
use Weew\Console\Commands\HelpCommand;
use Weew\Console\Commands\ListCommand;
use Weew\Console\Commands\VersionCommand;
use Weew\Console\Console;
use Weew\Console\Exceptions\InvalidCommandException;
use Weew\Console\ICommandInvoker;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\Input;
use Weew\Console\IOutput;
use Weew\Console\Output;
use Weew\Console\OutputFormat;
use Weew\Console\OutputVerbosity;
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

    function it_shows_help(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(HelpCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('help');
    }

    function it_lists_commands(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(ListCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('list');
    }

    function it_lists_commands_by_default(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(ListCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('');
    }

    function it_shows_help_for_command(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(HelpCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('help list');
    }

    function it_shows_help_with_flag(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(HelpCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('list --help');
    }

    function it_shows_help_without_command_name(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(HelpCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('--help');
    }

    function it_shows_version(ICommandInvoker $invoker) {
        $this->getOutput()->setEnableBuffering(true);
        $this->setCommandInvoker($invoker);
        $invoker->run(
            Argument::type(VersionCommand::class),
            Argument::type(IInput::class),
            Argument::type(IOutput::class),
            Argument::type(IConsole::class)
        )->shouldBeCalled();

        $this->parseString('-V');
    }

    function it_detect_verbosity() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('');
        $this->getOutput()->getOutputVerbosity()->shouldBe(OutputVerbosity::NORMAL);
        $this->parseString('-v');
        $this->getOutput()->getOutputVerbosity()->shouldBe(OutputVerbosity::VERBOSE);
        $this->parseString('-vv');
        $this->getOutput()->getOutputVerbosity()->shouldBe(OutputVerbosity::DEBUG);
        $this->parseString('-v -1');
        $this->getOutput()->getOutputVerbosity()->shouldBe(OutputVerbosity::SILENT);
    }

    function it_detects_silent_mode() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('-s');
        $this->getOutput()->getOutputVerbosity()->shouldBe(OutputVerbosity::SILENT);
    }

    function it_detects_format() {
        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('-f raw');
        $this->getOutput()->getOutputFormat()->shouldBe(OutputFormat::RAW);
        $this->parseString('-f plain');
        $this->getOutput()->getOutputFormat()->shouldBe(OutputFormat::PLAIN);
        $this->parseString('');
        $this->getOutput()->getOutputFormat()->shouldBe(OutputFormat::NORMAL);
    }

    function it_handles_command_errors() {
        $command = new ErrorCommand();
        $consoleCommand = new Command('error');
        $consoleCommand->setHandler($command);
        $this->addCommand($consoleCommand);

        $this->getOutput()->setEnableBuffering(true);
        $this->parseString('error');
    }
}
