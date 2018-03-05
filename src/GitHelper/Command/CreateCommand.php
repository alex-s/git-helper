<?php

namespace GitHelper\Command;

use chobie\Jira\Api;
use chobie\Jira\Issues\Walker;
use Sync\JiraApi\AdvancedApi;
use Sync\JiraApi\AdvancedCurlClient;
use Sync\JiraApi\HtAccessCookieAuth;

class CreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('create branch by pattern by jira issue number')
        ;
    }

    protected function executeCommand()
    {
        $jiraApi = new AdvancedApi($params['jira_url'],
            new HtAccessCookieAuth($params['jira_login'], $params['jira_password'], $params['htaccess_user'], $params['htaccess_pass']),
            new AdvancedCurlClient()
        );

        $walker = new Walker($api);
    }
}