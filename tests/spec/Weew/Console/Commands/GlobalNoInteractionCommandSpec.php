<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalNoInteractionCommand;
use Weew\Console\IInput;
use Weew\Console\InputVerbosity;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalNoInteractionCommand
 */
class GlobalNoInteractionCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalNoInteractionCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(true);
    }

    function it_does_nothing_without_flag(IInput $input, IOutput $output) {
        $input->getOption('--no-interaction')->willReturn(false);
        $this->run($input, $output);
    }

    function it_detects_no_interaction_mode(IInput $input, IOutput $output) {
        $input->getOption('--no-interaction')->willReturn(true);
        $input->setInputVerbosity(InputVerbosity::SILENT)->shouldBeCalled();
        $this->run($input, $output);
    }
}
