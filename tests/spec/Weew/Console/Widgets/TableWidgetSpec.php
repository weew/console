<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\TableWidget;

/**
 * @mixin TableWidget
 */
class TableWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);

        $this
            ->setIndent(2)
            ->setGutter(1)
            ->setSectionIndent(1)
            ->setVerticalSeparator('|');

        $this
            ->setTitle('title')
            ->addRow('key', 'value')
            ->addSection('section')
            ->addRow('another key', 'another value')
            ->addSection('another section')
            ->addRow(['text value', 'some value']);

        $this->render();
    }
}
