<?php

namespace TreeBundle\Command;

use AppBundle\Tree\LeafBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConsoleTreeCalculatorCommand extends Command
{
    const CREATE_COMMAND = 'create';
    const ADD_LEVEL_COMMAND = 'add-level';
    const SUM_LEAF = 'sum';
    const STOP = 'stop';

    protected function configure()
    {
        $this
            ->setName('tree:init')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Welcome!');
        $consoleStarted = true;
        $treeList = [];
        do {
            $userInput = $this->getHelper('question')->ask($input, $output, new Question(''));
            $extractedName = '';

            if ($this->extractName($userInput) !== '') {
                $extractedName = $this->extractName($userInput);
            }

            if ($this->extractCommand($userInput) === self::CREATE_COMMAND) {

                $treeList[$extractedName] = '';
                $output->writeln('Leaf ' . '<info>' . $extractedName . '</info>' . ' is created');
            }

            if ($this->extractCommand($userInput) === self::ADD_LEVEL_COMMAND) {

                //First set must have only 1 leaf
                if (!array_key_exists($extractedName, $treeList)) {
                    $output->writeln('You must create a leaf first');
                }
                //
                $treeList[$extractedName] .= $this->extractLevelConfig($userInput) . ';';
                $output->writeln('<info>' . $extractedName . '</info>' . ' configuration now is: ' . $treeList[$extractedName]);
            }

            if ($this->extractCommand($userInput) === self::SUM_LEAF) {

                $leafBuilder = new LeafBuilder();
                $treeConfig = substr($treeList[$extractedName], 0, -1);

                $leaf = $leafBuilder->buildTree($treeConfig);
                $output->writeln('Max sum is: ' . $leaf->findMaxSum());
            }

            if ($this->extractCommand($userInput) === self::STOP) {
                $consoleStarted = false;
                $output->writeln('Goodbye...');
            }

        } while ($consoleStarted === true);
    }

    /**
     * @param string $userInput
     * @return string
     */
    private function extractCommand($userInput)
    {
        $userInput = explode(' ', $userInput);
        switch ($userInput[0]) {
            case self::CREATE_COMMAND:
                return self::CREATE_COMMAND;
            case self::ADD_LEVEL_COMMAND:
                return self::ADD_LEVEL_COMMAND;
            case self::SUM_LEAF:
                return self::SUM_LEAF;
            case self::STOP:
                return self::STOP;
        }
    }

    /**
     * @param string $userInput
     * @return string
     */
    private function extractName($userInput)
    {
        $userInput = explode(' ', $userInput);
        return isset($userInput[1]) ? $userInput[1] : '';
    }

    /**
     * @param string $userInput
     * @return string
     */
    private function extractLevelConfig($userInput)
    {
        $userInput = explode(' ', $userInput);
        return $userInput[2];
    }
}