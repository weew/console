<?php

namespace Weew\Console;

use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\Exceptions\ArgumentNotFoundException;
use Weew\ConsoleArguments\Exceptions\OptionNotFoundException;
use Weew\ConsoleArguments\ICommand;

class Input implements IInput {
    /**
     * @var ICommand
     */
    protected $command;

    /**
     * @var int
     */
    protected $inputVerbosity;

    /**
     * @var resource
     */
    protected $inputStream;

    /**
     * Input constructor.
     *
     * @param ICommand $command
     */
    public function __construct(ICommand $command = null) {
        if ( ! $command instanceof ICommand) {
            $command = new Command();
        }

        $this->command = $command;
        $this->setInputVerbosity(InputVerbosity::NORMAL);
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function getArgument($name, $default = null) {
        try {
            $argument = $this->getCommand()->findArgument($name);
            $value = $argument->getValue();

            if ($value === null) {
                $value = $default;
            }

            return $value;
        } catch (ArgumentNotFoundException $ex) {}

        return $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setArgument($name, $value) {
        $argument = $this->getCommand()->findArgument($name);
        $argument->setValue($value);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasArgument($name) {
        try {
            $argument = $this->getCommand()->findArgument($name);

            return $argument->hasValue();
        } catch (ArgumentNotFoundException $ex) {}

        return false;
    }

    /**
     * @param $nameOrAlias
     * @param null $default
     *
     * @return mixed|null
     */
    public function getOption($nameOrAlias, $default = null) {
        try {
            $option = $this->getCommand()->findOption($nameOrAlias);
            $value = $option->getValue();

            if ($value === null) {
                $value = $default;
            }

            return $value;
        } catch (OptionNotFoundException $ex) {}

        return $default;
    }

    /**
     * @param string $nameOrAlias
     * @param mixed $value
     */
    public function setOption($nameOrAlias, $value) {
        $option = $this->getCommand()->findOption($nameOrAlias);
        $option->setValue($value);
    }

    /**
     * @param string $nameOrAlias
     *
     * @return bool
     */
    public function hasOption($nameOrAlias) {
        try {
            $option = $this->getCommand()->findOption($nameOrAlias);

            return $option->hasValue();
        } catch (OptionNotFoundException $ex) {}

        return false;
    }

    /**
     * @return string
     */
    public function readLine() {
        if ( ! $this->is(InputVerbosity::SILENT)) {
            return trim(fgets($this->getInputStream()));
        }

        return '';
    }

    /**
     * @return string
     */
    public function readChar() {
        if ( ! $this->is(InputVerbosity::SILENT)) {
            return fgetc($this->getInputStream());
        }

        return '';
    }

    /**
     * @return ICommand
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * @param ICommand $command
     */
    public function setCommand(ICommand $command) {
         $this->command = $command;
    }

    /**
     * @return int
     */
    public function getInputVerbosity() {
        return $this->inputVerbosity;
    }

    /**
     * @param int $inputVerbosity
     *
     * @see InputVerbosity
     */
    public function setInputVerbosity($inputVerbosity) {
        $this->inputVerbosity = $inputVerbosity;
    }

    /**
     * @param $options
     *
     * @return bool
     */
    protected function is($options) {
        return ($this->getInputVerbosity() & $options) === $options;
    }


    /**
     * @return resource
     */
    protected function getInputStream() {
        if ($this->inputStream === null) {
            $this->inputStream = fopen('php://stdin', 'r');
        }

        return $this->inputStream;
    }
}
