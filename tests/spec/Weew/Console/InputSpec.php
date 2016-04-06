<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\IInput;
use Weew\Console\Input;
use Weew\Console\InputVerbosity;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\ICommand;

/**
 * @mixin Input
 */
class InputSpec extends ObjectBehavior {
    function let() {
        $command = new Command('name');
        $command->argument(ArgumentType::SINGLE, 'arg1')->setValue('val1');
        $command->argument(ArgumentType::SINGLE, 'arg2');
        $command->option(ArgumentType::SINGLE, '--opt1')->setValue('val2');
        $command->option(ArgumentType::SINGLE, null, '-o')->setValue('val3');
        $command->option(ArgumentType::SINGLE, '--opt2');

        $this->beConstructedWith($command);
    }

    function it_is_initializable() {
        $this->shouldHaveType(Input::class);
    }

    function it_can_be_constructed_without_command() {
        $this->beConstructedWith();
        $this->getCommand()->shouldHaveType(ICommand::class);
    }

    function it_implements_iinput() {
        $this->beAnInstanceOf(IInput::class);
    }

    function it_takes_command_trough_the_constructor() {
        $command = new Command('name');
        $this->beConstructedWith($command);

        $this->getCommand()->shouldBe($command);
    }

    function it_takes_and_returns_a_command() {
        $command = new Command();
        $this->getCommand()->shouldHaveType(ICommand::class);
        $this->setCommand($command);
        $this->getCommand()->shouldBe($command);
    }

    function it_returns_argument_value() {
        $this->getArgument('arg1')->shouldBe('val1');
        $this->getArgument('arg2')->shouldBe(null);
    }

    function it_returns_null_if_argument_cant_be_found() {
        $this->getArgument('some_arg')->shouldBe(null);
        $this->getArgument('some_arg', 'value')->shouldBe('value');
    }

    function it_returns_argument_with_custom_default_value() {
        $this->getArgument('arg1', 'val')->shouldBe('val1');
        $this->getArgument('arg2', 'val')->shouldBe('val');
    }

    function it_can_tell_if_it_has_an_argument_value() {
        $this->hasArgument('arg1')->shouldBe(true);
        $this->hasArgument('arg2')->shouldBe(false);
    }

    function it_returns_false_if_an_argument_cant_be_found() {
        $this->hasArgument('some_arg')->shouldBe(false);
    }

    function it_takes_argument_value() {
        $this->setArgument('arg1', 'val');
        $this->getArgument('arg1')->shouldBe('val');
    }

    function it_returns_option_value() {
        $this->getOption('--opt1')->shouldBe('val2');
        $this->getOption('-o')->shouldBe('val3');
        $this->getOption('--opt2')->shouldBe(null);
    }

    function it_returns_null_if_option_cant_be_found() {
        $this->getOption('--some_option')->shouldBe(null);
        $this->getOption('--some_option', 'value')->shouldBe('value');
    }

    function it_returns_option_with_custom_default_value() {
        $this->getOption('--opt1', 'val')->shouldBe('val2');
        $this->getOption('-o', 'val')->shouldBe('val3');
        $this->getOption('--opt2', 'val')->shouldBe('val');
    }

    function it_can_tell_if_it_has_an_option_value() {
        $this->hasOption('--opt1')->shouldBe(true);
        $this->hasOption('-o')->shouldBe(true);
        $this->hasOption('--opt2')->shouldBe(false);
    }

    function it_returns_false_if_an_option_cant_be_found() {
        $this->hasOption('--some_option')->shouldBe(false);
    }

    function it_takes_option_value() {
        $this->setOption('--opt1', 'val');
        $this->getOption('--opt1')->shouldBe('val');
    }

    function it_takes_and_returns_input_verbosity() {
        $this->getInputVerbosity()->shouldBe(InputVerbosity::NORMAL);
        $this->setInputVerbosity(InputVerbosity::SILENT);
        $this->getInputVerbosity()->shouldBe(InputVerbosity::SILENT);
    }

    function it_reads_line() {
        $this->setInputVerbosity(InputVerbosity::SILENT);
        $this->readLine()->shouldBe('');
    }

    function it_reads_char() {
        $this->setInputVerbosity(InputVerbosity::SILENT);
        $this->readChar()->shouldBe('');
    }
}
