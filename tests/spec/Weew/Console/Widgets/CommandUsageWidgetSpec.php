<?php

namespace tests\spec\Weew\Console\Widgets;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Weew\Console\Input;
use Weew\Console\Output;
use Weew\Console\Widgets\CommandUsageWidget;
use Weew\ConsoleArguments\ArgumentType;
use Weew\ConsoleArguments\Command;
use Weew\ConsoleArguments\OptionType;

/**
 * @mixin CommandUsageWidget
 */
class CommandUsageWidgetSpec extends ObjectBehavior {
    function it_renders() {
        $command = new Command();
        $command->argument(ArgumentType::SINGLE, 'arg1');
        $command->argument(ArgumentType::SINGLE_OPTIONAL, 'arg2');
        $command->argument(ArgumentType::MULTIPLE, 'arg3');
        $command->argument(ArgumentType::MULTIPLE_OPTIONAL, 'arg4');
        $command->option(OptionType::SINGLE, '--opt1', '-1');
        $command->option(OptionType::SINGLE_OPTIONAL, '--opt2', '-2');
        $command->option(OptionType::MULTIPLE, '--opt3', '-3');
        $command->option(OptionType::MULTIPLE_OPTIONAL, '--opt4', '-4');
        $command->option(OptionType::BOOLEAN, '--opt5', '-5');
        $command->option(OptionType::INCREMENTAL, '--opt6', '-6');

        $input = new Input();
        $output = new Output();
        $output->setEnableBuffering(true);

        $this->beConstructedWith($input, $output);
        $this->render($command);
    }
}
