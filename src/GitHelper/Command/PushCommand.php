<?php

namespace GitHelper\Command;

class PushCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('push')
            ->setDescription('Push current branch')
        ;
    }

    protected function executeCommand()
    {
        $this->getGit()->push('origin', $this->getGit()->getBranches()->head());
    }
}