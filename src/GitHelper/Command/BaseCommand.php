<?php

namespace GitHelper\Command;

use GitHelper\MonologConsoleOutputHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        $logger = new Logger('git');
        $logger->pushHandler(new MonologConsoleOutputHandler($this->output));

        return $logger;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getArgument($name)
    {
        return $this->input->getArgument($name);
    }
}