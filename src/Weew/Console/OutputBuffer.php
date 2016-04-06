<?php

namespace Weew\Console;

class OutputBuffer implements IOutputBuffer {
    /**
     * @var array
     */
    protected $buffer = '';

    /**
     * Add a string to buffer.
     *
     * @param string $string
     */
    public function write($string) {
        if ( ! is_array($string)) {
            $string = [$string];
        }

        foreach ($string as $item) {
            $this->buffer .= $item;
        }
    }

    /**
     * Get a slice from the buffer.
     *
     * @param int $from
     * @param int $length
     *
     * @return string|false
     */
    public function slice($from, $length = null) {
        if ($length === null) {
            return substr($this->buffer, $from);
        }

        return substr($this->buffer, $from, $length);
    }

    /**
     * Get buffer contents and clear the buffer.
     *
     * @return string
     */
    public function flush() {
        $buffer = $this->reveal();
        $this->clear();

        return $buffer;
    }

    /**
     * Get buffer contents.
     *
     * @return string
     */
    public function reveal() {
        return $this->buffer;
    }

    /**
     * Clear all buffer contents.
     */
    public function clear() {
        $this->buffer = '';
    }

    /**
     * Returns current buffer length.
     *
     * @return int
     */
    public function getLength() {
        return strlen($this->buffer);
    }
}
