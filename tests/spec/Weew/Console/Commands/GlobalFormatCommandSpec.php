<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\GlobalFormatCommand;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputFormat;
use Weew\ConsoleArguments\Command;

/**
 * @mixin GlobalFormatCommand
 */
class GlobalFormatCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(GlobalFormatCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->isGlobal())->shouldBe(true);
        it($command->isHidden())->shouldBe(true);
    }

    function it_does_nothing_if_format_is_missing(IInput $input, IOutput $output) {
        $input->hasOption('--format')->willReturn(false);
        $this->run($input, $output);
    }

    function it_detects_plain_format(IInput $input, IOutput $output) {
        $input->hasOption('--format')->willReturn(true);
        $input->getOption('--format')->willReturn('plain');
        $output->setOutputFormat(OutputFormat::PLAIN)->shouldBeCalled();

        $this->run($input, $output);
    }

    function it_detects_raw_format(IInput $input, IOutput $output) {
        $input->hasOption('--format')->willReturn(true);
        $input->getOption('--format')->willReturn('raw');
        $output->setOutputFormat(OutputFormat::RAW)->shouldBeCalled();

        $this->run($input, $output);
    }

    function it_defaults_to_normal_format(IInput $input, IOutput $output) {
        $input->hasOption('--format')->willReturn(true);
        $input->getOption('--format')->willReturn(null);
        $output->setOutputFormat(OutputFormat::NORMAL)->shouldBeCalled();

        $this->run($input, $output);
    }
}
