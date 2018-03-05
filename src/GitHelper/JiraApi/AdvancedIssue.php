<?php
namespace GitHelper\JiraApi;

use chobie\Jira\Issue;

class AdvancedIssue extends Issue
{
    protected $issueKey;
    protected $number;
    protected $name;
    protected $type;

    public function __construct(array $issue = [])
    {
        parent::__construct($issue);

        $this->type = $this->fields['issuetype']['name'];
        $this->name = $this->fields['summary'];
        $this->issueKey = explode('-', $this->key)[0];
        $this->number = explode('-', $this->key)[1];

    }

    public function getBranchNameByPattern($pattern, $limit)
    {
        $name = str_replace(' ', '-', $this->name);
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);

        if (strlen($name) > $limit) {
            $name = substr($name, 0, strrpos($name, '-', -1 * (strlen($name) - $limit) ));
        }

        $name = strtr($pattern, [
            '%type%' => strtolower($this->type) === 'bug' ? 'bug': 'feature',
            '%name%' => $name,
            '%number%' => $this->number,
            '%key%' => $this->issueKey,
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