<?php

namespace Weew\Console;

interface IOutputBuffer {
    /**
     * Add a string to buffer.
     *
     * @param string $string
     */
    function write($string);

    /**
     * Get buffer contents.
     *
     * @return string
     */
    function reveal();

    /**
     * Get buffer contents and clear the buffer.
     *
     * @return string
     */
    function flush();

    /**
     * Get a slice from the buffer.
     *
     * @param int $from
     * @param int $length
     *
     * @return string|false
     */
    function slice($from, $length = null);

    /**
     * Clear all buffer contents.
     */
    function clear();

    /**
     * Returns current buffer length.
     *
     * @return int
     */
    function getLength();
}
