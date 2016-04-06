<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Console;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\ConsoleDescriptionWidget;

/**
 * @mixin ConsoleDescriptionWidget
 */
class ConsoleDescriptionWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $console = new Console();
        $console->setTitle(null);
        $console->setDescription(null);

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($console);
    }
}
