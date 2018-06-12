<?php

namespace GitHelper;


use Bitbucket\API\Repositories\PullRequests;

class ExtendedPullRequests extends PullRequests
{
    public function diffstat($account, $repo, $id)
    {
        return $this->getClient()->setApiVersion('2.0')->get(
            sprintf('repositories/%s/%s/pullrequests/%d/diffstat', $account, $repo, $id)
        );
    }
}