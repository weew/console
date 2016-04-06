<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\CommandArgumentsWidget;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandArgumentsWidget
 */
class CommandArgumentsWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $command = new Command();
        $command->argument(ArgumentType::SINGLE, 'single');
        $command->argument(ArgumentType::SINGLE_OPTIONAL, 'single_optional');
        $command->argument(ArgumentType::MULTIPLE, 'multiple');
        $command->argument(ArgumentType::MULTIPLE_OPTIONAL, 'multiple_optional');

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($command);
    }
}
