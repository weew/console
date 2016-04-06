<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\CommandDescriptionWidget;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandDescriptionWidget
 */
class CommandDescriptionWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $command = new Command('command', 'description');
        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($command);
    }
}
