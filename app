#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new \GitHelper\Command\FindCommand());
$application->add(new \GitHelper\Command\ReviewCommand());
$application->add(new \GitHelper\Command\CreateCommand());

$application->run();