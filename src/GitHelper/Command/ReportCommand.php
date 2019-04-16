<?php

namespace GitHelper\Command;

use chobie\Jira\Api;
use chobie\Jira\Issues\Walker;
use GuzzleHttp\Client;

class ReportCommand extends BaseCommand
{
    private $namesMap = [
        'Alex Schastniy',
        'Alexander Shevchuk',
        'Alexey Buldyk',
        'XSEM Dev Pool',
    ];

    private $priorityOrder = [
        '', //need for array_search return value > 0
        'Immediate',
        'Urgent',
        'High',
        'Moderate',
        'Normal',
        'Low',
    ];

    private $statusOrder = [
        '',//need for array_search return value > 0
        'In Progress',
        'Reopened',
        'To Do',
        'Blocked',
        'In peer review',
        'Ready for QA',
        'QA In Progress',
    ];

    protected function configure()
    {
        $this
            ->setName('report')
            ->setDescription('print report by current week')
        ;
    }

    protected function executeCommand()
    {
        $params = $this->getParameters();

        $url = $this->getParameters()['eh_url'];
        $project = $this->getParameters()['eh_project_key'];
        $key = $this->getParameters()['eh_api_key'];

        $client = new Client(['base_uri' => $url]);
        $response = $client->request('GET', 'projects/' . $project . '/tasks', ['headers' => ['X-Api-Key' => $key]]);
        $timingsData = json_decode($response->getBody()->getContents(), true);
        $api = new Api($params['url'],
            new Api\Authentication\Basic($params['login'], $params['password'])
        );

        $walker = new Walker($api);
        $filter = <<<FILTER
            project = XSEM 
            AND (fixVersion in unreleasedVersions() OR fixVersion is EMPTY) 
            AND status not in (Backlog, "Ready For Refinement", "QA: Passed", "Not on roadmap", Done, Merged) 
            AND type not in (Epic)
FILTER;

        $walker->push($filter);
        $issuesData = [];
        $issuesTimings = [];

        foreach ($walker as $issue) {
            if (!in_array($issue->getAssignee()['displayName'], $this->namesMap)) {
                continue;
            }
            $issueKey = $issue->getKey() . ' ' . $issue->getSummary();
            $issuesTimings[$issueKey] = ['estimate' => 0, 'reported' => 0];
            $statusOrder = array_search($issue->getStatus()['name'], $this->statusOrder) ?: 99;
            $priorityOrder = array_search($issue->getPriority()['name'], $this->priorityOrder) ?: 99;

            $issuesData[$issue->getAssignee()['displayName']][$statusOrder][$priorityOrder][] = [
                'key' => $issueKey,
                'number' => str_replace('XSEM-', '', $issue->getKey()),
                'title' => $issue->getSummary(),
                'status' => $issue->getStatus()['name'],
                'priority' => $issue->getPriority()['name'],
            ];
        }

        foreach ($timingsData as $time) {
            if (array_key_exists($time['name'], $issuesTimings)) {
                if (array_key_exists('time', $time)) {
                    $issuesTimings[$time['name']]['reported'] = $time['time']['total'] / (60 * 60);
                }
                if (array_key_exists('estimate', $time)) {
                    $issuesTimings[$time['name']]['estimate'] = $time['estimate']['total'] / (60 * 60);
                }
            }
        }

        foreach ($issuesData as $name => $statuses) {
            print($name . PHP_EOL);
            ksort($statuses);

            foreach ($statuses as $priorities) {
                ksort($priorities);

                foreach ($priorities as $issues) {
                    foreach ($issues as $issue) {
                        print vsprintf("%s\t%s\t%s\t%s\t%d\t \t%.1f" . PHP_EOL, [
                            $issue['number'], $issue['title'], $issue['status'], $issue['priority'],
                            $issuesTimings[$issue['key']]['estimate'], $issuesTimings[$issue['key']]['reported'],
                        ]);
                    }
                }
            }
        }

    }
}