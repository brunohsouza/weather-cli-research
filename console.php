#!/usr/bin/php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Weather\Controller\WeatherCommandController;

$application = new Application();
$application->add(new WeatherCommandController());

try {
    $application->run();
} catch (Exception $exception) {
    return $exception->getMessage();
}
