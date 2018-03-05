<?php

namespace GitHelper\Command;

use GitHelper\JiraApi\AdvancedApi;
use GitHelper\JiraApi\AdvancedCurlClient;
use GitHelper\JiraApi\AdvancedIssue;
use GitHelper\JiraApi\HtAccessCookieAuth;
use Symfony\Component\Console\Input\InputArgument;

class CreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('create')
            ->addArgument('number',InputArgument::REQUIRED)
            ->setDescription('create branch by pattern by jira issue number')
        ;
    }

    protected function executeCommand()
    {
        $params = $this->getParameters();

        $api = new AdvancedApi($params['url'],
            new HtAccessCookieAuth($params['login'], $params['password'], $params['htaccess_user'], $params['htaccess_pass']),
            new AdvancedCurlClient()
        );

        /** @var AdvancedIssue $issue */
        $issue = $api->getIssue($params['project_key'] . '-' . $this->getArgument('number'));
        $branch = $issue->getBranchNameByPattern($params['branch_template'], $params['branch_length_limit']);

        $this->getGit()->checkoutNewBranch($branch);
    }
}