<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\OutputBuffer;

/**
 * @mixin OutputBuffer
 */
class OutputBufferSpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(OutputBuffer::class);
    }

    function it_writes_and_returns_buffer() {
        $this->reveal()->shouldBe('');
        $this->write('some');
        $this->reveal()->shouldBe('some');
        $this->write('text');
        $this->reveal()->shouldBe('sometext');
    }

    function it_clears_buffer() {
        $this->write('text');
        $this->reveal()->shouldBe('text');
        $this->clear();
        $this->reveal()->shouldBe('');
    }

    function it_returns_buffer_length() {
        $this->getLength()->shouldBe(0);
        $this->write('text');
        $this->getLength()->shouldBe(4);
    }

    function it_returns_a_slice_of_buffer() {
        $this->write('123456');
        $this->slice(1)->shouldBe('23456');
        $this->slice(2, -1)->shouldBe('345');
    }

    function it_flushes() {
        $this->write('text');
        $this->flush()->shouldBe('text');
        $this->reveal()->shouldBe('');
    }
}
