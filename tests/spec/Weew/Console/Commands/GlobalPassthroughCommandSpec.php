<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalPassthroughCommand;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalPassthroughCommand
 */
class GlobalPassthroughCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalPassthroughCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(true);
    }

    function it_does_nothing_if_passthrough_is_missing(
        IInput $input,
        IOutput $output,
        IConsole $console
    ) {
        $console->setCatchErrors(false)->shouldNotBeCalled();
        $input->getOption('--passthrough')->willReturn(false);
        $this->run($input, $output, $console);
    }

    function it_disables_error_catching(
        IInput $input,
        IOutput $output,
        IConsole $console
    ) {
        $console->setCatchErrors(false)->shouldBeCalled();
        $input->getOption('--passthrough')->willReturn(true);
        $this->run($input, $output, $console);
    }
}
