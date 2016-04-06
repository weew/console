<?php

namespace Weew\Console;

/**
 * Bit masks inside this class should
 * be aware of those in the OutputFormat class.
 */
class OutputVerbosity {
    /**
     * normal: 0001
     */
    const NORMAL = 0x01;

    /**
     * verbose: 0011
     */
    const VERBOSE = 0x03;

    /**
     * debug: 0111
     */
    const DEBUG = 0x07;

    /**
     * silent: 1000
     */
    const SILENT = 0x08;

    /**
     * @param int $level
     *
     * @return int
     */
    public static function getVerbosityForLevel($level) {
        if ($level < 0) {
            return self::SILENT;
        }

        if ($level === 0) {
            return self::NORMAL;
        }

        if ($level === 1) {
            return self::VERBOSE;
        }

        return self::DEBUG;
    }
}
