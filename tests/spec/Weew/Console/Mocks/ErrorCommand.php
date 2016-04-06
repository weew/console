<?php

namespace tests\spec\Weew\Console\Mocks;

use Exception;
use Weew\Console\IConsole;
use Weew\Console\IInput;
use Weew\Console\IOutput;

class ErrorCommand extends FakeCommand {
    public function run(IInput $input, IOutput $output, IConsole $console) {
        throw new Exception('fake exception');
    }
}
