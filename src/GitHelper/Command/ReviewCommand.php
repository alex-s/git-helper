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
        $git->fetch();

        $remoteBranches = $this->findBranches($search, true);
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


    protected function getAfterExecuteCommands()
    {
        return [
            'app/console cache:clear',
            'app/console assetic:dump',
            'bin/console cache:clear',
            'bin/console assetic:dump',
        ];
    }
}