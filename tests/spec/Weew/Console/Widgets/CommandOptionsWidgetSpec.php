<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\CommandOptionsWidget;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\OptionType;

/**
 * @mixin CommandOptionsWidget
 */
class CommandOptionsWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $command = new Command();
        $command->option(OptionType::SINGLE, '--name', '-a');
        $command->option(OptionType::INCREMENTAL, '--name', '-n');

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($command);
    }
}
