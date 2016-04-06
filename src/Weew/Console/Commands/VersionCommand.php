<?php

namespace Weew\Console\Commands;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class VersionCommand {
    /**
     * @var IConsole
     */
    private $console;

    /**
     * VersionCommand constructor.
     *
     * @param IConsole $console
     */
    public function __construct(IConsole $console) {
        $this->console = $console;
    }

    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setName('version')
            ->setDescription('Show application version')
            ->setHidden(true);

        $command->option(OptionType::BOOLEAN, '--version', '-V')
            ->setDescription('Show application version');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     */
    public function run(IInput $input, IOutput $output) {
        $version = $this->console->getVersion();

        $output->writeLine(
            "<keyword>Application version $version</keyword>"
        );
    }
}
