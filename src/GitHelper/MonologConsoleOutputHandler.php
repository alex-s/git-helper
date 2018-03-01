<?php

namespace GitHelper;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class MonologConsoleOutputHandler extends AbstractProcessingHandler
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     * @param int $level The minimum logging level at which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(OutputInterface $output, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->output = $output;
        $this->setFormatter(new LineFormatter("%datetime% %level_name%: %message%", null, true));
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->output->writeln((string) $record['formatted']);
    }
}
