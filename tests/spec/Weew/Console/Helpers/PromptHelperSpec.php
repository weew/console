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
        $this->ask('question', false)->shouldBe(false);
        $input->readLine()->willReturn('');
        $this->ask('question', true)->shouldBe(true);
        $input->readLine()->willReturn('', '', 'y');
        $this->ask('question')->shouldBe(true);
    }

    function it_chooses(IInput $input) {
        $input->readLine()->willReturn(1);
        $this->choose('question', [
            'key1' => 'option1',
            'key2' => 'option2',
        ])->shouldBe('key1');

        $input->readLine()->willReturn(2);
        $this->choose('question', [
            'key1' => 'option1',
            'key2' => 'option2',
        ])->shouldBe('key2');

        $input->readLine()->willReturn(3, 4, 1);
        $this->choose('question', [
            'key1' => 'option1',
            'key2' => 'option2',
        ])->shouldBe('key1');
    }
}
