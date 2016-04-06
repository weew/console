<?php

namespace tests\spec\Weew\Console\Mocks;

use Exception;
use Weew\Console\IInput;
use Weew\Console\IOutput;

class ErrorCommand extends FakeCommand {
    public function run(IInput $input, IOutput $output) {
        throw new Exception('fake exception');
    }
}
