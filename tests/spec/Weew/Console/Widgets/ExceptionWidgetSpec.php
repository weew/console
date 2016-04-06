<?php

namespace tests\spec\Weew\Console\Widgets;

use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\ExceptionWidget;

/**
 * @mixin ExceptionWidget
 */
class ExceptionWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $ex = new Exception();
        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($ex);
    }
}
