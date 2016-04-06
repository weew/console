<?php

namespace Weew\Console\Widgets;

use Exception;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputVerbosity;

class ExceptionWidget {
    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * ExceptionWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param Exception $ex
     */
    public function render(Exception $ex) {
        $message = $ex->getMessage();
        $stackTrace = $ex->getTraceAsString();
        $class = get_class($ex);
        $file = $ex->getFile();
        $line = $ex->getLine();

        $this->output->writeLine("<error>$message</error>");

        $this->output->writeLine('', OutputVerbosity::DEBUG);
        $this->output->writeLine("<error>$class</error>", OutputVerbosity::DEBUG);
        $this->output->writeLine("<error>In $file on $line</error>", OutputVerbosity::DEBUG);
        $this->output->writeLine('', OutputVerbosity::DEBUG);
        $this->output->writeLine("<error>$stackTrace</error>", OutputVerbosity::DEBUG);
    }
}
