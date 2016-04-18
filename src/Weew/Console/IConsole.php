<?php

namespace Weew\Console;

use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleFormatter\IConsoleFormatter;

interface IConsole {
    /**
     * @return string
     */
    function getTitle();

    /**
     * @param string $title
     *
     * @return IConsole
     */
    function setTitle($title);

    /**
     * @return string
     */
    function getDescription();

    /**
     * @param string $description
     *
     * @return IConsole
     */
    function setDescription($description);

    /**
     * @return string
     */
    function getVersion();

    /**
     * @param string $version
     *
     * @return IConsole
     */
    function setVersion($version);

    /**
     * @return ICommand[]
     */
    function getCommands();

    /**
     * @param object[] $commands
     */
    function setCommands(array $commands);

    /**
     * @param object[] $commands
     */
    function addCommands(array $commands);

    /**
     * @param object $command
     */
    function addCommand($command);

    /**
     * @return string
     */
    function getDefaultCommandName();

    /**
     * @param string $commandName
     */
    function setDefaultCommandName($commandName);

    /**
     * @param array $argv
     */
    function parseArgv(array $argv = null);

    /**
     * @param array $args
     */
    function parseArgs(array $args);

    /**
     * @param $string
     */
    function parseString($string);

    /**
     * @return IConsoleFormatter
     */
    function getConsoleFormatter();

    /**
     * @param IConsoleFormatter $consoleFormatter
     */
    function setConsoleFormatter(IConsoleFormatter $consoleFormatter);

    /**
     * @return IOutput
     */
    function getOutput();

    /**
     * @param IOutput $output
     */
    function setOutput(IOutput $output);

    /**
     * @return IInput
     */
    function getInput();

    /**
     * @param IInput $input
     */
    function setInput(IInput $input);

    /**
     * @return ICommandInvoker
     */
    function getCommandInvoker();

    /**
     * @param ICommandInvoker $commandInvoker
     */
    function setCommandInvoker(ICommandInvoker $commandInvoker);
}
