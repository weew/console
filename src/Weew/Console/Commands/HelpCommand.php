<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\Widgets\CommandArgumentsWidget;
use Weew\Console\Widgets\CommandDescriptionWidget;
use Weew\Console\Widgets\CommandHelpWidget;
use Weew\Console\Widgets\CommandOptionsWidget;
use Weew\Console\Widgets\CommandUsageWidget;
use Weew\Console\Widgets\GlobalOptionsWidget;
use Weew\ConsoleArguments\ArgumentsMatcher;
use Weew\ConsoleArguments\ArgumentsParser;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\Exceptions\AmbiguousCommandException;
use Weew\ConsoleArguments\Exceptions\CommandNotFoundException;
use Weew\ConsoleArguments\ICommand;

class HelpCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setName('help')
            ->setDescription('Show help text')
            ->setHelp(<<<EOT
To get help for a command run
 <keyword>help \<command></keyword>

You can also use the --help option
 <keyword>\<command> --help</keyword>
EOT
            );

        $command->argument(ArgumentType::SINGLE_OPTIONAL, 'command')
            ->setDescription('Command to show help for');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     * @param IConsole $console
     */
    public function run(IInput $input, IOutput $output, IConsole $console) {
        $command = $this->findCommand($input, $console);

        $widget = new CommandDescriptionWidget($input, $output);
        $widget->render($command);

        $widget = new CommandUsageWidget($input, $output);
        $widget->render($command);

        $widget = new GlobalOptionsWidget($input, $output, $console);
        $widget->render();

        $widget = new CommandArgumentsWidget($input, $output);
        $widget->render($command);

        $widget = new CommandOptionsWidget($input, $output);
        $widget->render($command);

        $widget = new CommandHelpWidget($input, $output);
        $widget->render($command);
    }

    /**
     * @param IInput $input
     * @param IConsole $console
     *
     * @return ICommand
     * @throws AmbiguousCommandException
     * @throws CommandNotFoundException
     */
    protected function findCommand(IInput $input, IConsole $console) {
        if ($input->hasArgument('command')) {
            $matcher = new ArgumentsMatcher(new ArgumentsParser());

            return $matcher->findCommand(
                $console->getCommands(), $input->getArgument('command')
            );
        }

        return $input->getCommand();
    }
}
