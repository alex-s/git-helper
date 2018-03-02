<?php

namespace GitHelper\Command;

use Symfony\Component\Console\Input\InputArgument;

class FindCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('find')
            ->addArgument('search',InputArgument::REQUIRED)
            ->setDescription('Find branches by issue number')
        ;
    }

    protected function executeCommand()
    {
        $branches = $this->findBranches($this->getArgument('search'), false);

        $branch = $this->chooser($branches);

        $this->getGit()->checkout($branch);
    }
}