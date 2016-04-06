<?php

namespace Weew\Console;

use Weew\ConsoleFormatter\ConsoleFormatter;
use Weew\ConsoleFormatter\IConsoleFormatter;

class Output implements IOutput {
    /**
     * @var IConsoleFormatter
     */
    protected $formatter;

    /**
     * @var IOutputBuffer
     */
    protected $buffer;

    /**
     * @var int
     */
    protected $outputVerbosity;

    /**
     * @var int
     */
    protected $outputFormat;

    /**
     * @var bool
     */
    protected $enableBuffering = false;

    /**
     * @var resource
     */
    protected $outputStream;

    /**
     * Output constructor.
     *
     * @param IConsoleFormatter $formatter
     * @param IOutputBuffer $buffer
     */
    public function __construct(
        IConsoleFormatter $formatter = null,
        IOutputBuffer $buffer = null
    ) {
        if ( ! $formatter instanceof IConsoleFormatter) {
            $formatter = $this->createFormatter();
        }

        if ( ! $buffer instanceof IOutputBuffer) {
            $buffer = $this->createBuffer();
        }

        $this->setFormatter($formatter);
        $this->setBuffer($buffer);
        $this->setOutputVerbosity(OutputVerbosity::NORMAL);
        $this->setOutputFormat(OutputFormat::NORMAL);
        $this->setEnableBuffering(false);
    }

    /**
     * @param string $string
     * @param int $options
     *
     * @return IOutput
     *
     * @see OutputVerbosity
     * @see OutputProcessing
     */
    public function write($string, $options = null) {
        if ($options === null) {
            $options = OutputVerbosity::NORMAL;
        }

        if ($this->is($options)) {
            $this->buffer->write($string);

            if ( ! $this->isBufferingEnabled()) {
                $this->flushBuffer();
            }
        }

        return $this;
    }

    /**
     * @param string $string
     * @param int $options
     *
     * @return IOutput
     *
     * @see OutputVerbosity
     * @see OutputProcessing
     */
    public function writeLine($string = '', $options = null) {
        return $this->write($string . "\n", $options);
    }

    /**
     * @param string $string
     * @param int $spaces
     * @param null $options
     *
     * @return IOutput
     */
    public function writeIndented($string, $spaces = 2, $options = null) {
        return $this->write($this->indent($string, $spaces), $options);
    }

    /**
     * @param string $string
     * @param int $spaces
     * @param null $options
     *
     * @return IOutput
     */
    public function writeLineIndented($string, $spaces = 2, $options = null) {
        return $this->writeLine($this->indent($string, $spaces), $options);
    }

    /**
     * @param string $string
     * @param int $spaces
     *
     * @return string
     */
    public function indent($string, $spaces = 2) {
        $indentation = str_repeat(' ', $spaces);
        $lines = explode("\n", $string);

        foreach ($lines as $index => $line) {
            $lines[$index] = $indentation . $line;
        }

        return implode("\n", $lines);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function format($string) {
        // print output with ansi support
        if ($this->is(OutputFormat::NORMAL)) {
            return $this->formatter->formatAnsi($string);
        }
        // print output without ansi support
        else if ($this->is(OutputFormat::PLAIN)) {
            return $this->formatter->formatPlain($string);
        }

        // return raw output
        return $string;
    }

    /**
     * @return int
     */
    public function getOutputVerbosity() {
        return $this->outputVerbosity;
    }

    /**
     * @param int $outputVerbosity
     *
     * @see OutputVerbosity
     */
    public function setOutputVerbosity($outputVerbosity) {
        $this->outputVerbosity = $outputVerbosity;
    }

    /**
     * @return int
     */
    public function getOutputFormat() {
        return $this->outputFormat;
    }

    /**
     * @param int $outputFormat
     *
     * @see OutputProcessing
     */
    public function setOutputFormat($outputFormat) {
        $this->outputFormat = $outputFormat;
    }

    /**
     * Check whether output is being buffered.
     *
     * @return bool
     */
    public function isBufferingEnabled() {
        return $this->enableBuffering;
    }

    /**
     * Enable/disable output buffering.
     *
     * @param bool $enableBuffering
     */
    public function setEnableBuffering($enableBuffering) {
        $this->enableBuffering = !! $enableBuffering;
    }

    /**
     * Manually flush buffered output.
     */
    public function flushBuffer() {
        $content = $this->format($this->buffer->flush());

        if ( ! $this->is(OutputVerbosity::SILENT)) {
            fwrite($this->getOutputStream(), $content);
        }
    }

    /**
     * @return IConsoleFormatter
     */
    public function getFormatter() {
        return $this->formatter;
    }

    /**
     * @param IConsoleFormatter $formatter
     */
    public function setFormatter(IConsoleFormatter $formatter) {
        $this->formatter = $formatter;
    }

    /**
     * @return IOutputBuffer
     */
    public function getBuffer() {
        return $this->buffer;
    }

    /**
     * @param IOutputBuffer $buffer
     */
    public function setBuffer(IOutputBuffer $buffer) {
        $this->buffer = $buffer;
    }

    /**
     * @return resource
     */
    protected function getOutputStream() {
        if ($this->outputStream === null) {
            $this->outputStream = fopen('php://stdout', 'w');
        }

        return $this->outputStream;
    }

    /**
     * @param int $options
     *
     * @return bool
     */
    protected function is($options) {
        return ($this->getOutputVerbosity() & $options) === $options
            || ($this->getOutputFormat() & $options) === $options;
    }

    /**
     * @return IConsoleFormatter
     */
    protected function createFormatter() {
        return new ConsoleFormatter();
    }

    /**
     * @return IOutputBuffer
     */
    protected function createBuffer() {
        return new OutputBuffer();
    }
}
