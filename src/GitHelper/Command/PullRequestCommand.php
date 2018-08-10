<?php

namespace GitHelper\Command;

use Bitbucket\API\Http\Listener\BasicAuthListener;
use GitHelper\ExtendedPullRequests;

class PullRequestCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('pulls')
            ->setDescription('Find requests with conflicts')
        ;
    }

    protected function executeCommand()
    {
        $user = $this->getParameters()['bb_user'];
        $pass = $this->getParameters()['bb_pass'];
        $acc = $this->getParameters()['bb_account'];
        $repo = $this->getParameters()['bb_repo'];

        $pull = new ExtendedPullRequests();
        $pull->getClient()->addListener(
            new BasicAuthListener($user, $pass)
        );

        $pulls = $this->getPulls($pull, $acc, $repo);
        $conflicts = [];

        foreach ($pulls as $key => $pullParams) {
            $stat = json_decode($pull->diffstat($acc, $repo, $pullParams->id)->getContent());

            foreach ($stat->values as $file) {
                if (in_array($file->status, ['merge conflict', 'remote deleted', 'local deleted'])) {
                    if (!isset($conflicts[$pullParams->author->display_name])) {
                        $conflicts[$pullParams->author->display_name] = [];
                    }

                    $conflicts[$pullParams->author->display_name][] = $pullParams->links->html->href;

                    continue 2;
                }
            }
        }

        foreach (array_keys($conflicts) as $user) {
            $this->getLogger()->info($user);
            foreach ($conflicts[$user] as $pull) {
                $this->getLogger()->info('  ' . $pull);
            }
        }

        $this->getLogger()->info('Done');
    }

    protected function getPulls(ExtendedPullRequests $pull, $acc, $repo)
    {
        $page = 1;
        $this->getLogger()->info('Load PRs:');

        do {
            $this->getLogger()->info('  page ' . $page);
            $pulls = $pull->all($acc, $repo, ['page' => $page]);
            $pulls = json_decode($pulls->getContent());

            foreach ($pulls->values as $pullParams) {
                yield $pullParams;
            }

            $page = isset($pulls->next) ? $page + 1 : 0;
        } while ($page);
    }
}