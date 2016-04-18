<?php

namespace Weew\Console\Commands;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputFormat;
use Weew\ConsoleArguments\ICommand;
use Weew\ConsoleArguments\OptionType;

class GlobalFormatCommand {
    /**
     * @param ICommand $command
     */
    public function setup(ICommand $command) {
        $command->setGlobal(true)->setHidden(true);
        $command->option(OptionType::SINGLE_OPTIONAL, '--format', '-f')
            ->setDescription('Output format: normal, plain, raw');
    }

    /**
     * @param IInput $input
     * @param IOutput $output
     */
    public function run(IInput $input, IOutput $output) {
        if ($input->hasOption('--format')) {
            $format = $input->getOption('--format');

            if ($format === 'plain') {
                $output->setOutputFormat(OutputFormat::PLAIN);
            } else if ($format === 'raw') {
                $output->setOutputFormat(OutputFormat::RAW);
            } else {
                $output->setOutputFormat(OutputFormat::NORMAL);
            }
        }
    }
}
