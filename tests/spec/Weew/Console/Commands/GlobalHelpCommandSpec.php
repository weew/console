<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalHelpCommand;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalHelpCommand
 */
class GlobalHelpCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalHelpCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);

        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(false);
    }

    function it_does_nothing_without_flag(IInput $input, IOutput $output, IConsole $console) {
        $input->hasOption('--help')->willReturn(false);

        $this->run($input, $output, $console);
    }

    function it_runs_help_for_command(IInput $input, IOutput $output, IConsole $console) {
        $input->hasOption('--help')->willReturn(true);
        $input->getArgument('args')->willReturn(['name']);
        $console->parseString('help name')->shouldBeCalled();

        $this->run($input, $output, $console);
    }
}
