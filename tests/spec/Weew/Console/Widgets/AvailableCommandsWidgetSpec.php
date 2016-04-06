<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\IConsole;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\AvailableCommandsWidget;
use Weew\ConsoleArguments\Command;

/**
 * @mixin AvailableCommandsWidget
 */
class AvailableCommandsWidgetSpec extends ObjectBehavior {
    function it_renders(IConsole $console) {
        $console->getCommands()->willReturn([
            new Command('group1:name1'),
            new Command('group1:name2'),
            new Command('group2:name1'),
            new Command('name1'),
        ]);

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($console);
    }

    function it_renders_without_Commands(IConsole $console) {
        $console->getCommands()->willReturn([]);

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($console);
    }
}
