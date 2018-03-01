<?php

namespace GitHelper\Command;

use GitWrapper\Event\GitLoggerListener;
use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FindCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('git:find')
            ->addArgument('search',InputArgument::REQUIRED)
            ->setDescription('Find branches by issue number')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $logger = $this->getLogger();
        $wrapper = new GitWrapper();
//        $listener = new GitLoggerListener($logger);
//        $wrapper->addLoggerListener($listener);

        $git = new GitWorkingCopy($wrapper, getcwd());
        $branches = $git->getBranches();

        $results = [];
        foreach ($branches as $branch){
            $branch = str_replace('remotes/origin/', '', $branch);
            if (!in_array($branch, $results, true) && strpos($branch, $this->getArgument('search')) !== false) {
                $results[] = $branch;
            }
        }

        if (count($results) === 0) {
            $logger->info('Nothing found');
            exit();
        }

        if (count($results) === 1) {
            $git->checkout($results[0]);
            exit();
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Which branch should be choose',$results,0);
        $question->setErrorMessage('Wrong number');

        $branch = $helper->ask($input, $output, $question);
        $git->checkout($branch);
    }
}