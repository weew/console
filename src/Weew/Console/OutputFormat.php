<?php

namespace Weew\Console;

/**
 * Bit masks inside this class should
 * be aware of those in the OutputVerbosity class.
 */
class OutputFormat {
    /**
     * Apply formatting.
     *
     * normal: 0001 0000
     */
    const NORMAL = 0x10;

    /**
     * Do not apply formatting and strip
     * all formatting tags.
     *
     * plain: 0010 0000
     */
    const PLAIN = 0x20;

    /**
     * Do not apply formatting
     * and keep all formatting tags.
     *
     * raw: 0100 000
     */
    const RAW = 0x40;
}
