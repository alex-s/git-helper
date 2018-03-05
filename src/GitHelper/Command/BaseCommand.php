<?php

namespace GitHelper\Command;

use GitHelper\MonologConsoleOutputHandler;
use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

abstract class BaseCommand extends Command
{
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var GitWorkingCopy
     */
    protected $git;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->executeCommand();
    }

    abstract protected function executeCommand();

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if ( $this->logger === null) {
            $this->logger = new Logger('git');
            $this->logger->pushHandler(new MonologConsoleOutputHandler($this->output));
        }

        return  $this->logger;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @return GitWorkingCopy
     */
    protected function getGit()
    {
        if ( $this->git === null) {
            $this->git = new GitWorkingCopy(new GitWrapper(), getcwd());
        }

        return  $this->git;
    }

    /**
     * @param $search
     * @param $isRemote
     * @return array
     */
    protected function findBranches($search, $isRemote)
    {
        $branches = $this->getGit()->getBranches();

        $results = [];
        foreach ($branches as $branch){
            if ($isRemote && strpos($branch, 'master') !== false) {
                continue;
            }

            if (strpos($branch, $search) !== false && $this->isRemote($branch) === $isRemote) {
                $branch = str_replace('remotes/origin/', '', $branch);

                if (!in_array($branch, $results, true)) {
                    $results[] = $branch;
                }
            }
        }

        return $results;
    }

    /**
     * @param $branch
     * @return bool
     */
    private function isRemote($branch)
    {
        return strpos($branch, 'remotes/origin/') !== false;
    }

    protected function chooser($options)
    {
        if (count($options) === 0) {
            $this->getLogger()->info('Nothing found');
            exit();
        }

        if (count($options) === 1) {
           return $options[0];
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('What options should I choose', $options,0);
        $question->setErrorMessage('Wrong option number');

        return $helper->ask($this->input, $this->output, $question);
    }

    public function getParameters()
    {
        return parse_ini_file('params.ini');
    }
}