<?php

namespace Weew\Console;

use Exception;
use Weew\Console\Commands\HelpCommand;
use Weew\Console\Commands\ListCommand;
use Weew\Console\Commands\VersionCommand;
use Weew\Console\Exceptions\InvalidCommandException;
use Weew\Console\Widgets\ExceptionWidget;
use Weew\ConsoleArguments\ArgumentsMatcher;
use Weew\ConsoleArguments\ArgumentsParser;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\Exceptions\MissingCommandNameException;
use Weew\ConsoleArguments\IArgumentsMatcher;
use Weew\ConsoleArguments\IArgumentsParser;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\Option;
use Weew\ConsoleArguments\OptionType;
use Weew\ConsoleFormatter\ConsoleFormatter;
use Weew\ConsoleFormatter\IConsoleFormatter;

class Console implements IConsole {
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * @var object[]
     */
    protected $commands = [];

    /**
     * @var ICommandInvoker
     */
    protected $commandInvoker;

    /**
     * @var IArgumentsParser
     */
    protected $argumentsParser;

    /**
     * @var IArgumentsMatcher
     */
    protected $argumentsMatcher;

    /**
     * @var IConsoleFormatter
     */
    protected $consoleFormatter;

    /**
     * @var IOutput
     */
    protected $output;

    /**
     * @var IInput
     */
    protected $input;

    /**
     * Console constructor.
     *
     * @param ICommandInvoker $commandInvoker
     */
    public function __construct(ICommandInvoker $commandInvoker = null) {
        if ( ! $commandInvoker instanceof ICommandInvoker) {
            $commandInvoker = $this->createCommandInvoker();
        }

        $this->argumentsParser = new ArgumentsParser();
        $this->argumentsMatcher = new ArgumentsMatcher($this->argumentsParser);

        $this->setCommandInvoker($commandInvoker);
        $this->setConsoleFormatter(new ConsoleFormatter());
        $this->setOutput(new Output($this->consoleFormatter));
        $this->setInput(new Input());

        $this->addDefaultCommands();
        $this->addDefaultStyles();
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return IConsole
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return IConsole
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return IConsole
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * @return ICommand[]
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * @param object[] $commands
     */
    public function setCommands(array $commands) {
        $this->commands = [];
        $this->addCommands($commands);
    }

    /**
     * @param object[] $commands
     */
    public function addCommands(array $commands) {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    /**
     * @param object $command
     */
    public function addCommand($command) {
        if ($command instanceof ICommand) {
            $consoleCommand = $command;
            $command = $consoleCommand->getHandler();
        } else {
            $consoleCommand = new Command();
        }

        $this->validateCommand($command);

        if ( ! is_object($command)) {
            $command = $this->commandInvoker->create($command);
        }

        $consoleCommand->setHandler($command);

        $this->commandInvoker->setup($command, $consoleCommand);
        $this->commands[] = $consoleCommand;
    }

    /**
     * @param array $argv
     */
    public function parseArgv(array $argv = null) {
        if ( ! is_array($argv)) {
            global $argv;
        }

        $this->parseArgs(array_slice($argv, 1));
    }

    /**
     * @param array $args
     */
    public function parseArgs(array $args) {
        $this->parseString(implode(' ', $args));
    }

    /**
     * @param $string
     */
    public function parseString($string) {
        $this->handleArgs($this->argumentsParser->parse($string));
    }

    /**
     * @return IConsoleFormatter
     */
    public function getConsoleFormatter() {
        return $this->consoleFormatter;
    }

    /**
     * @param IConsoleFormatter $consoleFormatter
     */
    public function setConsoleFormatter(IConsoleFormatter $consoleFormatter) {
        $this->consoleFormatter = $consoleFormatter;
    }

    /**
     * @return IOutput
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @param IOutput $output
     */
    public function setOutput(IOutput $output) {
        $this->output = $output;
    }

    /**
     * @return IInput
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param IInput $input
     */
    public function setInput(IInput $input) {
        $this->input = $input;
    }

    /**
     * @return ICommandInvoker
     */
    public function getCommandInvoker() {
        return $this->commandInvoker;
    }

    /**
     * @param ICommandInvoker $commandInvoker
     */
    public function setCommandInvoker(ICommandInvoker $commandInvoker) {
        $this->commandInvoker = $commandInvoker;
    }

    /**
     * @param array $args
     */
    protected function handleArgs(array $args) {
        $this->detectOptions($args);

        try {
            if ($this->interruptCommand($args)) {
                return;
            }

            $command = $this->argumentsMatcher->matchCommands($this->commands, $args);
            $this->runCommand($command);
        } catch (MissingCommandNameException $ex) {
            array_unshift($args, $this->getDefaultCommandName());
            $this->parseArgs($args);
        } catch (Exception $ex) {
            $widget = new ExceptionWidget($this->input, $this->output);
            $widget->render($ex);
        }
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function interruptCommand(array $args) {
        return $this->interruptForHelp($args)
            || $this->interruptForVersion($args);
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function interruptForHelp(array $args) {
        $groupedArgs = $this->argumentsParser->group($args);
        $helpOption = new Option(OptionType::BOOLEAN, '--help', '-h');
        $this->argumentsMatcher->matchOption($helpOption, $groupedArgs);

        list($commandName, $args) = $this->argumentsMatcher->matchCommandName($args);

        if ($commandName === 'help') {
            return false;
        }

        if ($helpOption->hasValue() && $commandName) {
            $this->parseString(s('help %s', $commandName));

            return true;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function interruptForVersion(array $args) {
        $groupedArgs = $this->argumentsParser->group($args);
        $versionOption = new Option(OptionType::BOOLEAN, '--version', '-V');
        $this->argumentsMatcher->matchOption($versionOption, $groupedArgs);

        if ($versionOption->hasValue()) {
            $this->parseString('version');

            return true;
        }

        return false;
    }

    /**
     * @param ICommand $command
     */
    protected function runCommand(ICommand $command) {
        try {
            $input = clone $this->input;
            $input->setCommand($command);

            $this->commandInvoker->run(
                $command->getHandler(),
                $input,
                $this->output
            );
        } catch (Exception $ex) {
            $widget = new ExceptionWidget($this->input, $this->output);
            $widget->render($ex);
        }
    }

    /**
     * @param $command
     *
     * @throws InvalidCommandException
     */
    protected function validateCommand($command) {
        if ( ! is_string($command) && ! is_object($command)) {
            throw new InvalidCommandException(s(
                'Command must be either a class name or an instance, "%s" given.',
                get_type($command)
            ));
        }

        if (is_string($command) && ! class_exists($command)) {
            throw new InvalidCommandException(s(
                'Command "%s" does not exist.',
                $command
            ));
        }

        if ( ! method_exists($command, 'setup') || ! method_exists($command, 'run')) {
            throw new InvalidCommandException(s(
                'Command "%s" must implement methods "setup" and "run".',
                get_type($command)
            ));
        }
    }


    /**
     * @return ICommandInvoker
     */
    protected function createCommandInvoker() {
        return new CommandInvoker();
    }

    /**
     * Register default command handlers.
     */
    protected function addDefaultCommands() {
        $this->addCommands([
            new ListCommand($this),
            new HelpCommand($this),
            new VersionCommand($this),
        ]);
    }

    /**
     * @param array $args
     */
    protected function detectOptions($args) {
        $this->detectVerbosity($args);
        $this->detectOutputFormat($args);
        $this->detectSilentMode($args);
    }

    /**
     * @param array $args
     */
    protected function detectSilentMode(array $args) {
        $args = $this->argumentsParser->group($args);
        $option = new Option(OptionType::BOOLEAN, '--silent', '-s');
        $this->argumentsMatcher->matchOption($option, $args);

        if ($option->getValue()) {
            $this->output->setOutputVerbosity(OutputVerbosity::SILENT);
        }
    }

    /**
     * @param array $args
     */
    protected function detectVerbosity(array $args) {
        $option = new Option(OptionType::INCREMENTAL, '--verbosity', '-v');
        $this->argumentsMatcher->matchOption($option, $args);

        $this->output->setOutputVerbosity(
            OutputVerbosity::getVerbosityForLevel($option->getValue())
        );
    }

    /**
     * @param array $args
     */
    protected function detectOutputFormat(array $args) {
        $args = $this->argumentsParser->group($args);
        $option = new Option(OptionType::SINGLE_OPTIONAL, '--format', '-f');
        $this->argumentsMatcher->matchOption($option, $args);

        if ($option->getValue() === 'plain') {
            $this->output->setOutputFormat(OutputFormat::PLAIN);
        } else if ($option->getValue() === 'raw') {
            $this->output->setOutputFormat(OutputFormat::RAW);
        } else {
            $this->output->setOutputFormat(OutputFormat::NORMAL);
        }
    }

    /**
     * @return string
     */
    protected function getDefaultCommandName() {
        return 'list';
    }

    /**
     * Register default formatter styles.
     */
    protected function addDefaultStyles() {
        $this->consoleFormatter->style('error')->parseStyle('clr=white bg=red');
        $this->consoleFormatter->style('warning')->parseStyle('clr=black bg=yellow');
        $this->consoleFormatter->style('success')->parseStyle('clr=black bg=green');

        $this->consoleFormatter->style('question')->parseStyle('clr=green');
        $this->consoleFormatter->style('header')->parseStyle('clr=yellow');
        $this->consoleFormatter->style('keyword')->parseStyle('clr=green');

        $this->consoleFormatter->style('green')->parseStyle('clr=green');
        $this->consoleFormatter->style('yellow')->parseStyle('clr=yellow');
        $this->consoleFormatter->style('red')->parseStyle('clr=red');
        $this->consoleFormatter->style('white')->parseStyle('clr=white');
        $this->consoleFormatter->style('blue')->parseStyle('clr=blue');
        $this->consoleFormatter->style('gray')->parseStyle('clr=gray');
        $this->consoleFormatter->style('black')->parseStyle('clr=black');
    }
}
