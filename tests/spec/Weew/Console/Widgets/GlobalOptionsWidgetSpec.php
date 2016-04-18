<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Console;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\GlobalOptionsWidget;

/**
 * @mixin GlobalOptionsWidget
 */
class GlobalOptionsWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output, new Console());
        $this->render();
    }
}
