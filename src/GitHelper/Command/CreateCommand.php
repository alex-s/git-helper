<?php

namespace GitHelper\Command;

use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;
use chobie\Jira\Issue;
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

        $api = new Api($params['url'],
            new Basic($params['login'], $params['password'])
        );

        $response = $api->getIssue($params['project_key'] . '-' . $this->getArgument('number'));

        $branch = $this->getBranchNameByPattern($response->getResult(), $params['branch_template'], $params['branch_length_limit']);

        $this->getGit()->checkoutNewBranch($branch);
    }

    public function getBranchNameByPattern($result, $pattern, $limit)
    {
        $issue = new Issue($result);

        $type = $issue->getFields()['issuetype']['name'];
        $name = $issue->getFields()['summary'];
        list($issueKey, $number) = explode('-', $issue->getKey());

        $name = str_replace(' ', '-', strtolower($name));
        $name = str_replace(':', '-', strtolower($name));
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
        $name = preg_replace('/-+/', '-', $name);

        $name = strtr($pattern, [
            '%type%' => strtolower($type) === 'bug' ? 'bug': 'feature',
            '%name%' => $name,
            '%number%' => $number,
            '%key%' => $issueKey,
        ]);

        if (strpos($name, '%') !== false) {
            throw new \Exception('Looks like using unavailable variable in pattern');
        }

        if (strlen($name) > $limit) {
            $name = substr($name, 0, strrpos($name, '-', -1 * (strlen($name) - $limit)));
        }

        return $name;
    }
}