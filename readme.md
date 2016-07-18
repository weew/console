# Console application

[![Build Status](https://img.shields.io/travis/weew/console.svg)](https://travis-ci.org/weew/console)
[![Code Quality](https://img.shields.io/scrutinizer/g/weew/console.svg)](https://scrutinizer-ci.com/g/weew/console)
[![Test Coverage](https://img.shields.io/coveralls/weew/console.svg)](https://coveralls.io/github/weew/console)
[![Version](https://img.shields.io/packagist/v/weew/console.svg)](https://packagist.org/packages/weew/console)
[![Licence](https://img.shields.io/packagist/l/weew/console.svg)](https://packagist.org/packages/weew/console)

## Table of contents

- [Installation](#installation)
- [Introduction](#introduction)
- [Console](#console)
- [Input](#input)
- [Output](#output)
- [Widgets](#widgets)
    - [TableWidget](#tablewidget)
- [Helpers](#helpers)
    - [PromptHelper](#prompthelper)
- [Related projects](#related-projects)

## Installation

`composer require weew/console`

## Introduction

![Screenshot](screenshot.png?raw=true)

This package provides a convenient skeleton for console application. Optically, heavily inspired by the symfony console it has it similarities and differences. Describing commands, parsing console arguments, styling and colorization of output, question helpers etc., all this is included inside this package. In [related projects](#related-projects) you'll find a list of used components that you might also use separately from this package.

Note: this package has not been tested on windows. Windows contributions are highly appreciated.

## Console

To start building your console app, you need to instantiated a new console first.

```php
$console = new Console();
```

## Commands

Commands are the pieces of logic that you might plug and play into your console application. A command can by anything. There is no interface contract that you must fulfill. This design choice was made because of the dependency injection support for the [weew/console-container-aware](https://github.com/weew/console-formatter) package.

Your command must have the `setup` and `run` method. For further information about configuration of commands refer to the [weew/console-arguments](https://github.com/weew/console-arguments) package.

```php
class SampleCommand {
    public function setup(ICommand $command) {
        // describe command

        $command->setName('colors')
            ->setDescription('Shows a list of colors');

        $command->argument(ArgumentType::SINGLE, 'favorite_color');
        $command->argument(ArgumentType::SINGLE_OPTIONAL, '--only-light');
        $command->argument(ArgumentType::SINGLE_OPTIONAL, '--only-dark');
    }

    public function run(IInput $input, IOutput $output, IConsole $console) {
        // do your thang

        if ( ! $input->hasOption('--only-dark')) {
            $output->writeLine('<red>red</red>');
        }

        if ( ! $input->hasOption('--only-light')) {
            $output->writeLine('<blue>blue</blue>');
        }

        if ($input->hasArgument('favorite_color')) {
            $favoriteColor = $input->getArgument('favorite_color');
            $output->writeLine("The best of all colors is <keyword>$favoriteColor</keyword>");
        }
    }
}
```

All the important functionality is available trough instances of `IInput` and `IOutput`. There are many more things you can do, just take a look at it. All you have to do now is to register your command on the console.

```php
$console->addCommand(SampleCommand::class);

// or

$console->addCommand(new SampleCommand());
```

Running your command is as easy as pie.

```php
$console->parseString('colors red --only-dark');

// or

$console->parseArgs(['colors', 'red', '--only-dark'];

// or

$console->parseArgv(['./file_name', 'colors', 'red', '--only-dark']);
```

## Input

Input contains all the information about the received arguments and offers some apis for interaction with the user.

This is how you can retrieve the current command:

```php
$input->getCommand();
```

You can access arguments and options that were matched for the command:

```php
$input->getArgument('argument');
$input->getOption('--option');
```

You can prompt user for input using this methods:

```php
$input->readline();
$input->readChar();
```

## Output

Output is used to print information to the terminal. It uses the [weew/console-formatter](https://github.com/weew/console-formatter) for styling and formatting of the text.

```php
$output->writeLine('<keyword>key: </keyword> value');
$output->write('some text);
```

## Widgets

Widgets are small and reusable classes that serve as a kind of ui elements.

### TableWidget

This widget allows you to easily print simple tables.

```php
$table = new TableWidget($input, $output);
$table
    ->setTitle('Table title')
    ->addRow('task1', 'x')
    ->addSection('Done tasks')
    ->addRow('task2', '✓');

$table->render();
```

## Helpers

There are several helpers that you might use for interaction with the user.

### PromptHelper

This helper allows you to prompt user for different kinds of input.

```php
$prompt = new PromptHelper($input, $output);
$response = $prompt->ask('Are you ready?');
$response = $prompt->prompt('What is your name');
```

## Related projects

- [Console formatter](https://github.com/weew/console-formatter) Used for styling of output
- [Console arguments](https://github.com/weew/console-arguments) Used for parsing of arguments
- [Container aware console](https://github.com/weew/console-container-aware) Container aware version of this package
