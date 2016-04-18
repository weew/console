<?php

namespace Weew\Console;

use Exception;
use Weew\Console\Commands\GlobalFormatCommand;
use Weew\Console\Commands\GlobalHelpCommand;
use Weew\Console\Commands\GlobalNoInteractionCommand;
use Weew\Console\Commands\GlobalSilentModeCommand;
use Weew\Console\Commands\GlobalVerbosityCommand;
use Weew\Console\Commands\HelpCommand;
use Weew\Console\Commands\ListCommand;
use Weew\Console\Commands\GlobalVersionCommand;
use Weew\Console\Exceptions\InvalidCommandException;
use Weew\Console\Widgets\ExceptionWidget;
use Weew\ConsoleArguments\ArgumentsMatcher;
use Weew\ConsoleArguments\ArgumentsParser;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\Exceptions\MissingCommandNameException;
use Weew\ConsoleArguments\IArgumentsMatcher;
use Weew\ConsoleArguments\IArgumentsParser;
use Weew\ConsoleArguments\ICommand;
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
     * @var string
     */
    protected $defaultCommandName = 'list';

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
     * @return string
     */
    public function getDefaultCommandName() {
        return $this->defaultCommandName;
    }

    /**
     * @param string $commandName
     */
    public function setDefaultCommandName($commandName) {
        $this->defaultCommandName = $commandName;
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
        $groupedArgs = $this->argumentsParser->group($args);
        list($groupedArgs, $continue) = $this->runGlobalCommands($groupedArgs);

        if ($continue === false) {
            return;
        }

        try {
            list($command, $groupedArgs) = $this->argumentsMatcher
                ->matchCommands($this->getNotGlobalCommands(), $groupedArgs);
            $command = clone $command;
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
     * @param ICommand $command
     * @param bool $isolate
     *
     * @return mixed
     */
    protected function runCommand(ICommand $command, $isolate = true) {
        try {
            if ($isolate) {
                $input = clone $this->input;
                $output = clone $this->output;
            } else {
                $input = $this->input;
                $output = $this->output;
            }

            $input->setCommand($command);

            return $this->commandInvoker->run(
                $command->getHandler(), $input, $output, $this
            );
        } catch (Exception $ex) {
            $widget = new ExceptionWidget($this->input, $this->output);
            $widget->render($ex);
        }
    }

    /**
     * @param array $groupedArgs
     *
     * @return bool
     */
    protected function runGlobalCommands(array $groupedArgs) {
        // run commands that are global but will for sure
        // not generate any output or interrupt the flow
        foreach ($this->getGlobalHiddenCommands() as $command) {
            $command = clone $command;
            $groupedArgs = $this->argumentsMatcher->matchCommand($command, $groupedArgs, false);
            $this->runCommand($command, false);
        }

        // run commands that might generate output or
        // try to interrupt the flow
        foreach ($this->getGlobalNotHiddenCommands() as $command) {
            $command = clone $command;

            // dirty hack, fix later
            // global commands should not steal arguments
            $args = $groupedArgs['arguments'];
            $groupedArgs = $this->argumentsMatcher->matchCommand($command, $groupedArgs, false);
            $groupedArgs['arguments'] = $args;
            $continue = $this->runCommand($command);

            if ($continue === false) {
                return [$groupedArgs, false];
            }
        }

        return [$groupedArgs, true];
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
            new GlobalVersionCommand(),
            new GlobalFormatCommand(),
            new GlobalSilentModeCommand(),
            new GlobalNoInteractionCommand(),
            new GlobalHelpCommand(),
            new GlobalVerbosityCommand(),
            new ListCommand(),
            new HelpCommand(),
        ]);
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

    /**
     * @return ICommand[]
     */
    protected function getGlobalHiddenCommands() {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            if ($command->isGlobal() && $command->isHidden()) {
                $commands[] = $command;
            }
        }

        return $commands;
    }

    /**
     * @return ICommand[]
     */
    protected function getGlobalNotHiddenCommands() {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            if ($command->isGlobal() && ! $command->isHidden()) {
                $commands[] = $command;
            }
        }

        return $commands;
    }

    /**
     * @return ICommand[]
     */
    protected function getNotGlobalCommands() {
        $commands = [];

        foreach ($this->getCommands() as $command) {
            if ( ! $command->isGlobal()) {
                $commands[] = $command;
            }
        }

        return $commands;
    }
}
