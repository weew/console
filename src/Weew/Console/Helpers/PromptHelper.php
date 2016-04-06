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

        $this->output->writeIndented($question);

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
    public function ask($string, $default = false) {
        $suffix = $default ? 'Y/n' : 'y/N';

        $this->output->writeIndented(
            "<question>$string</question> [<yellow>$suffix</yellow>]: "
        );
        $input = $this->input->readLine();

        if ($input == 'y' or $input == 'yes') {
            return true;
        } else if (empty($input) and $default) {
            return true;
        }else {
            return false;
        }
    }
}
