<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalVersionCommand;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalVersionCommand
 */
class GlobalVersionCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalVersionCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(false);
    }

    function it_does_nothing_without_flag(IInput $input, IOutput $output, IConsole $console) {
        $this->run($input, $output, $console);
    }
    
    function it_shows_version(IInput $input, IOutput $output, IConsole $console) {
        $input->getOption('--version')->willReturn(true);
        $output->writeLine(Argument::type('string'))->shouldBeCalled();
        $this->run($input, $output, $console);
    }
}
