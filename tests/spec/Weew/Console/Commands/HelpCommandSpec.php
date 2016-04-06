<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\HelpCommand;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\Output;
use Weew\ConsoleArguments\Command;

/**
 * @mixin HelpCommand
 */
class HelpCommandSpec extends ObjectBehavior {
    function let(IConsole $console) {
        $console->getCommands()->willReturn([
            new Command('test'),
        ]);
        $this->beConstructedWith($console);
    }

    function it_is_initializable() {
        $this->shouldHaveType(HelpCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->getName())->shouldBe('help');
    }

    function it_runs(IInput $input) {
        $input->hasArgument('command')->willReturn(true);
        $input->getArgument('command')->willReturn('test');

        $output = new Output();
        $output->setEnableBuffering(true);
        $this->run($input, $output);
    }

    function it_uses_self_as_command(IInput $input) {
        $input->hasArgument('command')->willReturn(false);
        $input->getCommand()->willReturn(new Command());

        $output = new Output();
        $output->setEnableBuffering(true);
        $this->run($input, $output);
    }
}
