<?php

namespace tests\spec\Weew\Console\Commands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Commands\ListCommand;
use Weew\ConsoleArguments\Command;

/**
 * @mixin ListCommand
 */
class ListCommandSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(ListCommand::class);
    }

    function it_setups() {
        $command = new Command();
        $this->setup($command);
        it($command->getName())->shouldBe('list');
    }
}
