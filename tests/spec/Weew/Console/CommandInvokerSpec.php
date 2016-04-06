<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;
use tests\spec\Weew\Console\Mocks\FakeCommand;
use Weew\Console\CommandInvoker;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandInvoker
 */
class CommandInvokerSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(CommandInvoker::class);
    }

    function it_creates_handler() {
        $this->create(stdClass::class)->shouldHaveType(stdClass::class);
    }

    function it_setups_handler(FakeCommand $fakeCommand) {
        $command = new Command();
        $fakeCommand->setup($command)->shouldBeCalled();
        
        $this->setup($fakeCommand, $command);
    }

    function it_runs_handler(FakeCommand $fakeCommand) {
        $input = new Input();
        $output = new Output();

        $fakeCommand->run($input, $output)->shouldBeCalled();

        $this->run($fakeCommand, $input, $output);
    }
}
