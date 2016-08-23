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

        if ($input === null) {
            return $this->prompt($string, $default);
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

    /**
     * @param string $string
     * @param array $choices
     * @param bool $useChoiceKeysAsSelector
     *
     * @return mixed
     */
    public function choose($string, array $choices, $useChoiceKeysAsSelector = false) {
        $this->output->writeLine("<question>$string</question>");

        $choicesMap = [];

        foreach ($choices as $subject => $message) {
            if ($useChoiceKeysAsSelector) {
                $index = $subject;
            } else {
                $index = count($choicesMap) + 1;
            }

            $choicesMap[$index] = [
                'subject' => $subject,
                'message' => $message,
            ];
        }

        foreach ($choicesMap as $index => $choice) {
            $message = array_get($choice, 'message');

            $this->output->writeLineIndented("[<yellow>$index</yellow>] $message");
        }

        $choice = null;

        while (true) {
            $this->output->writeIndented('Choice: ');
            $input = $this->input->readLine();

            if (array_has($choicesMap, $input)) {
                $choice = array_get(array_get($choicesMap, $input), 'subject');
                break;
            }

            if ( ! empty($input)) {
                $this->output->writeLineIndented('<yellow>Invalid choice</yellow>');
            }
        }

        return $choice;
    }
}
