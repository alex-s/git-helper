<?php

namespace GitHelper\Command;

use Symfony\Component\Console\Input\InputArgument;

class KillCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('kill')
            ->addArgument('search',InputArgument::REQUIRED)
            ->setDescription('Find process to kill by name')
        ;
    }

    protected function executeCommand()
    {
        exec(sprintf('ps aux | grep %s', $this->getArgument('search')), $processesLines);

        $processes = [];

        foreach ($processesLines as $line) {
            if (strpos($line, 'kill') !== false || strpos($line, 'grep') !== false) {
                continue;
            }
            preg_match_all('/(\S+)[ ]+/', $line, $matches);
            $processes[] = $matches[1][1] . ' ' . $this->getProcessName($matches[1], 10);
        }

        $process = $this->chooser($processes);
        $pid = explode(' ', $process)[0];

        exec('kill ' . $pid);
    }

    private function getProcessName($matches, $index)
    {
        if (!isset($matches[$index])) {
            return '';
        }

        return trim($matches[$index]) . '_' . $this->getProcessName($matches, $index + 1);
    }
}