<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;

interface IInput {
    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    function getArgument($name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     */
    function setArgument($name, $value);

    /**
     * @param string $name
     *
     * @return bool
     */
    function hasArgument($name);

    /**
     * @param $nameOrAlias
     * @param null $default
     *
     * @return mixed
     */
    function getOption($nameOrAlias, $default = null);

    /**
     * @param string $nameOrAlias
     * @param mixed $value
     */
    function setOption($nameOrAlias, $value);

    /**
     * @param string $nameOrAlias
     *
     * @return bool
     */
    function hasOption($nameOrAlias);

    /**
     * @return string
     */
    function readLine();

    /**
     * @return string
     */
    function readChar();

    /**
     * @return int
     */
    function getInputVerbosity();

    /**
     * @param $inputVerbosity
     *
     * @see InputVerbosity
     */
    function setInputVerbosity($inputVerbosity);

    /**
     * @return ICommand
     */
    function getCommand();

    /**
     * @param ICommand $command
     */
    function setCommand(ICommand $command);
}
