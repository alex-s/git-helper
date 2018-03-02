<?php

namespace GitHelper\Command;

use Symfony\Component\Console\Input\InputArgument;

class ReviewCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('review')
            ->addArgument('search',InputArgument::REQUIRED)
            ->setDescription('Recreate issue branch and checkout last version from origin')
        ;
    }

    protected function executeCommand()
    {
        $git = $this->getGit();
        $search = $this->getArgument('search');


        $remoteBranches = $this->findBranches($search, true);

        if (count($remoteBranches) === 0) {
            $git->fetch();
            $remoteBranches = $this->findBranches($search, true);
        }

        $branch = $this->chooser($remoteBranches);

        if ($this->isLocalBranchExists($branch)) {
            $git->checkout('master');
            $git->branch($branch, ['D' => true]);
        }

        $git->checkout($branch);
    }

    private function isLocalBranchExists($branch)
    {
        $localBranches = $this->findBranches($branch, false);

        foreach ($localBranches as $localBranch) {
            if ($localBranch === $branch) {
                return true;
            }
        }

        return false;
    }
}