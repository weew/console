<?php

namespace tests\spec\Weew\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\IOutputBuffer;
use Weew\Console\Output;
use Weew\Console\OutputFormat;
use Weew\Console\OutputVerbosity;
use Weew\ConsoleFormatter\IConsoleFormatter;

/**
 * @mixin Output
 */
class OutputSpec extends ObjectBehavior {
    function let(
        IConsoleFormatter $formatter,
        IOutputBuffer $buffer
    ) {
        $this->beConstructedWith($formatter, $buffer);
    }

    function it_is_initializable() {
        $this->shouldHaveType(Output::class);
    }

    function it_takes_and_returns_formatter(IConsoleFormatter $formatter) {
        $this->beConstructedWith();
        $this->getFormatter()->shouldHaveType(IConsoleFormatter::class);
        $this->setFormatter($formatter);
        $this->getFormatter()->shouldBe($formatter);
    }

    function it_takes_and_returns_buffer(IOutputBuffer $buffer) {
        $this->beConstructedWith();
        $this->getBuffer()->shouldHaveType(IOutputBuffer::class);
        $this->setBuffer($buffer);
        $this->getBuffer()->shouldBe($buffer);
    }

    function it_enables_and_disables_buffering() {
        $this->isBufferingEnabled()->shouldBe(false);
        $this->setEnableBuffering(true);
        $this->isBufferingEnabled()->shouldBe(true);
    }

    function it_takes_verbosity_flag() {
        $this->getOutputVerbosity()->shouldBe(OutputVerbosity::NORMAL);
        $this->setOutputVerbosity(OutputVerbosity::DEBUG);
        $this->getOutputVerbosity()->shouldBe(OutputVerbosity::DEBUG);
    }

    function it_takes_processing_flag() {
        $this->getOutputFormat()->shouldBe(OutputFormat::NORMAL);
        $this->setOutputFormat(OutputFormat::RAW);
        $this->getOutputFormat()->shouldBe(OutputFormat::RAW);
    }

    function it_flushes_buffer(IOutputBuffer $buffer) {
        $buffer->flush()->shouldBeCalled();
        $this->flushBuffer();
    }

    function it_renders_output_according_to_processing_settings(
        IConsoleFormatter $formatter,
        IOutputBuffer $buffer
    ) {
        $buffer->flush()->willReturn('text1');
        $this->setOutputFormat(OutputFormat::NORMAL);
        $formatter->formatAnsi('text1')->shouldBeCalled();
        $buffer->flush()->shouldBeCalled();
        $this->flushBuffer();

        $buffer->flush()->willReturn('text2');
        $this->setOutputFormat(OutputFormat::PLAIN);
        $formatter->formatPlain('text2')->shouldBeCalled();
        $buffer->flush()->shouldBeCalled();
        $this->flushBuffer();

        $buffer->flush()->willReturn('');
        $this->setOutputFormat(OutputFormat::RAW);
        $formatter->formatPlain('')->shouldNotBeCalled();
        $buffer->flush()->shouldBeCalled();
        $this->flushBuffer();
    }

    function it_writes_without_flushing_the_buffer(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);
        $buffer->write('text')->shouldBeCalled();

        $this->write('text');
    }

    function it_writes_and_flushes_buffer(IOutputBuffer $buffer) {
        $buffer->write('text')->shouldBeCalled();
        $buffer->flush()->shouldBeCalled();

        $this->write('text');
    }

    function it_write_line(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);
        $buffer->write("text\n")->shouldBeCalled();

        $this->writeLine('text');
    }

    function it_writes_if_options_match(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);

        $buffer->write('text1')->shouldBeCalled();
        $this->write('text1');

        $buffer->write('text2')->shouldBeCalled();
        $this->write('text2', OutputVerbosity::NORMAL);
    }

    function it_does_not_write_if_option_do_not_match(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);
        $buffer->write('text')->shouldNotBeCalled();
        $this->write('text', OutputVerbosity::DEBUG);

        $this->setOutputVerbosity(OutputVerbosity::VERBOSE);
        $buffer->write('text2')->shouldNotBeCalled();
        $this->write('text2', OutputVerbosity::SILENT);

        $this->setOutputVerbosity(OutputVerbosity::SILENT);
        $buffer->write('text3')->shouldBeCalled();
        $this->write('text3', OutputVerbosity::SILENT);
    }

    function it_is_chainable_trough_the_write_method() {
        $this->write('')->shouldBe($this);
    }

    function it_is_chainable_trough_the_write_line_method() {
        $this->writeLine('')->shouldBe($this);
    }
    
    function it_writes_indented(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);
        $buffer->write("  first line \n   second line")->shouldBeCalled();
        $this->writeIndented("first line \n second line");
    }

    function it_is_chainable_trough_write_indented() {
        $this->writeIndented('string')->shouldBe($this);
    }

    function it_writes_line_indented(IOutputBuffer $buffer) {
        $this->setEnableBuffering(true);
        $buffer->write("  first line \n   second line\n")->shouldBeCalled();
        $this->writeLineIndented("first line \n second line");
    }

    function it_is_chainable_trough_write_line_indented() {
        $this->writeLineIndented('string')->shouldBe($this);
    }
    
    function it_indents() {
        $this->indent(" first line \n second line")
            ->shouldBe("   first line \n   second line");

        $this->indent(" first line \n second line", 3)
            ->shouldBe("    first line \n    second line");
    }
}
