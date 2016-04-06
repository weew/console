<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\CommandHelpWidget;
use Weew\ConsoleArguments\Command;

/**
 * @mixin CommandHelpWidget
 */
class CommandHelpWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $command = new Command();
        $command->setHelp('help');

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($command);
    }
}
