<?php

namespace GitHelper\Command;

class PullCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('pull')
            ->setDescription('Pull current branch')
        ;
    }

    protected function executeCommand()
    {
        $this->getGit()->pull('origin', $this->getGit()->getBranches()->head());
    }
}