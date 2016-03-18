#!/usr/bin/env php
<?php
/**
 *
 * This file is part of Producer for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Producer;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new ProducerContainer(
    $_SERVER['HOME'],
    getcwd(),
    STDOUT,
    STDERR
);

try {
    array_shift($argv);
    $name = array_shift($argv);
    $command = $container->newCommand($name);
    $exit = (int) $command($argv);
    exit($exit);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
