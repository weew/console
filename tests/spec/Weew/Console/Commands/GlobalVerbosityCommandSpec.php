<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalVerbosityCommand;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputVerbosity;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalVerbosityCommand
 */
class GlobalVerbosityCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalVerbosityCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(true);
    }

    function it_does_nothing_without_flag(IInput $input, IOutput $output) {
        $input->hasOption('--verbosity')->willReturn(false);
        $this->run($input, $output);
    }

    function it_detects_verbosity(IInput $input, IOutput $output) {
        $input->hasOption('--verbosity')->willReturn(true);
        $input->getOption('--verbosity', 0)->willReturn(3);
        $output->setOutputVerbosity(OutputVerbosity::DEBUG)->shouldBeCalled();
        $this->run($input, $output);
    }
}
