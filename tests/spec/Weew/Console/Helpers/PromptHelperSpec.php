<?php

namespace tests\spec\Weew\Console\Helpers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Helpers\PromptHelper;
use Weew\Console\IInput;
use Weew\Console\IOutput;

/**
 * @mixin PromptHelper
 */
class PromptHelperSpec extends ObjectBehavior {
    function let(IInput $input, IOutput $output) {
        $this->beConstructedWith($input, $output);
    }

    function it_is_initializable() {
        $this->shouldHaveType(PromptHelper::class);
    }

    function it_prompts(IInput $input) {
        $input->readLine()->willReturn('value');
        $this->prompt('question')->shouldBe('value');
        $input->readLine()->willReturn('');
        $this->prompt('question')->shouldBe(null);
        $input->readLine()->willReturn(null);
        $this->prompt('question', 'default')->shouldBe('default');
    }

    function it_asks(IInput $input) {
        $input->readLine()->willReturn('y');
        $this->ask('question')->shouldBe(true);
        $input->readLine()->willReturn('yes');
        $this->ask('question')->shouldBe(true);

        $input->readLine()->willReturn('n');
        $this->ask('question')->shouldBe(false);
        $input->readLine()->willReturn('no');
        $this->ask('question')->shouldBe(false);

        $input->readLine()->willReturn('');
        $this->ask('question')->shouldBe(false);
        $input->readLine()->willReturn('');
        $this->ask('question', true)->shouldBe(true);
    }
}
