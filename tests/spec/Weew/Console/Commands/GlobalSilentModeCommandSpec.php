<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalSilentModeCommand;
use Weew\Console\IInput;
use Weew\Console\InputVerbosity;
use Weew\Console\IOutput;
use Weew\Console\OutputVerbosity;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalSilentModeCommand
 */
class GlobalSilentModeCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalSilentModeCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(true);
    }

    function it_does_nothing_without_flag(IInput $input, IOutput $output) {
        $input->getOption('--silent')->willReturn(false);
        $this->run($input, $output);
    }

    function it_detects_silent_mode(IInput $input, IOutput $output) {
        $input->getOption('--silent')->willReturn(true);
        $input->setInputVerbosity(InputVerbosity::SILENT)->shouldBeCalled();
        $output->setOutputVerbosity(OutputVerbosity::SILENT)->shouldBeCalled();

        $this->run($input, $output);
    }
}
