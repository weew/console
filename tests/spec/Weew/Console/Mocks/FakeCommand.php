<?php

namespace tests\spec\Weew\Console\Mocks;

use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\ConsoleArguments\ICommand;

class FakeCommand {
    public function setup(ICommand $command) {
        return 'setup';
    }
    public function run(IInput $input, IOutput $output, IConsole $console) {
        return 'run';
    }
}
