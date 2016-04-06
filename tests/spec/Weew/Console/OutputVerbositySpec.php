<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\OutputVerbosity;

/**
 * @mixin OutputVerbosity
 */
class OutputVerbositySpec extends ObjectBehavior {
    function it_is_initializable() {
        $this->shouldHaveType(OutputVerbosity::class);
    }

    function it_returns_verbosity_for_levels() {
        $this->getVerbosityForLevel(-1)->shouldBe(OutputVerbosity::SILENT);
        $this->getVerbosityForLevel(0)->shouldBe(OutputVerbosity::NORMAL);
        $this->getVerbosityForLevel(1)->shouldBe(OutputVerbosity::VERBOSE);
        $this->getVerbosityForLevel(2)->shouldBe(OutputVerbosity::DEBUG);
        $this->getVerbosityForLevel(99)->shouldBe(OutputVerbosity::DEBUG);
    }

    function it_has_valid_masks() {
        it(OutputVerbosity::NORMAL & OutputVerbosity::VERBOSE)->shouldBe(OutputVerbosity::NORMAL);
        it(OutputVerbosity::NORMAL & OutputVerbosity::DEBUG)->shouldBe(OutputVerbosity::NORMAL);
        it(OutputVerbosity::VERBOSE & OutputVerbosity::DEBUG)->shouldBe(OutputVerbosity::VERBOSE);
        it(OutputVerbosity::NORMAL & OutputVerbosity::SILENT)->shouldBe(0);
        it(OutputVerbosity::VERBOSE & OutputVerbosity::SILENT)->shouldBe(0);
        it(OutputVerbosity::DEBUG & OutputVerbosity::SILENT)->shouldBe(0);
    }
}
