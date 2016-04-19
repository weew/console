<?php

namespace Weew\Console;

use Weew\ConsoleFormatter\IConsoleFormatter;

interface IOutput {
    /**
     * @param string $string
     * @param int $options
     *
     * @return IOutput
     *
     * @see OutputVerbosity
     * @see OutputProcessing
     */
    function write($string, $options = 0);

    /**
     * @param string $string
     * @param int $options
     *
     * @return IOutput
     *
     * @see OutputVerbosity
     * @see OutputProcessing
     */
    function writeLine($string = '', $options = 0);

    /**
     * @param string $string
     * @param int $spaces
     * @param null $options
     *
     * @return IOutput
     */
    function writeIndented($string, $spaces = 2, $options = null);

    /**
     * @param string $string
     * @param int $spaces
     * @param null $options
     *
     * @return IOutput
     */
    function writeLineIndented($string, $spaces = 2, $options = null);

    /**
     * @param string $string
     * @param int $spaces
     *
     * @return string
     */
    function indent($string, $spaces = 2);

    /**
     * @param string $string
     * @param null $format
     *
     * @return string
     */
    function format($string, $format = null);

    /**
     * @return int
     */
    function getOutputVerbosity();

    /**
     * @param int $verbosity
     *
     * @see OutputVerbosity
     */
    function setOutputVerbosity($verbosity);

    /**
     * @return int
     */
    function getOutputFormat();

    /**
     * @param int $outputFormat
     *
     * @see OutputProcessing
     */
    function setOutputFormat($outputFormat);

    /**
     * @return bool
     */
    function isBufferingEnabled();

    /**
     * Enable buffering of output.
     *
     * @param bool $enableBuffering
     */
    function setEnableBuffering($enableBuffering);

    /**
     * Flush buffer contents.
     */
    function flushBuffer();

    /**
     * @return IConsoleFormatter
     */
    function getFormatter();

    /**
     * @param IConsoleFormatter $formatter
     */
    function setFormatter(IConsoleFormatter $formatter);

    /**
     * @return IOutputBuffer
     */
    function getBuffer();

    /**
     * @param IOutputBuffer $buffer
     */
    function setBuffer(IOutputBuffer $buffer);
}
