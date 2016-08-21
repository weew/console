<?php

namespace Weew\Console\Helpers;

use Weew\Console\IInput;
use Weew\Console\IOutput;

class PromptHelper {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * PromptHelper constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param $string
     * @param string $default
     *
     * @return null|string
     */
    public function prompt($string, $default = null) {
        if ($default !== null) {
            $suffix = (string) $default;
            $question = "<question>$string</question> [<yellow>$suffix</yellow>]: ";
        } else {
            $question = "<question>$string</question>: ";
        }

        $this->output->write($question);
        $input = $this->input->readLine();

        if (strlen($input) == 0) {
            $input = $default;
        }

        return $input;
    }

    /**
     * @param $string
     * @param bool $default
     *
     * @return bool
     */
    public function ask($string, $default = null) {
        if ($default === true) {
            $suffix = 'Y/n';
        } else if ($default === false) {
            $suffix = 'y/N';
        } else {
            $suffix = 'y/n';
        }

        $this->output->write(
            "<question>$string</question> [<yellow>$suffix</yellow>]: "
        );
        $input = $this->input->readLine();

        if (array_contains(['yes', 'y'], $input)) {
            return true;
        }

        if (array_contains(['no', 'n'], $input)) {
            return false;
        }

        if (empty($input)) {
            if ($default === true) {
                return true;
            }

            if ($default === false) {
                return false;
            }
        }

        return $this->ask($string, $default);
    }
    }
}
