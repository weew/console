<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\VersionCommand;
use Weew\Console\IConsole;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\ConsoleArguments\Command;

/**
 * @mixin VersionCommand
 */
class VersionCommandSpec extends ObjectBehavior {
    function let(IConsole $console) {
        $this->beConstructedWith($console);
    }

    function it_is_initializable() {
        $this->shouldHaveType(VersionCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->getName())->shouldBe('version');
        it($command->isHidden())->shouldBe(true);
    }

    function it_runs() {
        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);
        $this->run($input, $output);
    }
}
