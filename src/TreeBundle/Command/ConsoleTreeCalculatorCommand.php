<?php

namespace TreeBundle\Command;

use AppBundle\Tree\Exception\LeafParseValidationException;
use AppBundle\Tree\LeafBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TreeBundle\Command\Exception\InvalidArgumentException;
use TreeBundle\Command\Exception\InvalidCommandException;

class ConsoleTreeCalculatorCommand extends Command
{
    const CREATE_COMMAND = 'create';
    const ADD_LEVEL_COMMAND = 'add-level';
    const SUM_LEAF = 'sum';
    const STOP = 'stop';

    const ALLOWED_COMMANDS = [
        self::CREATE_COMMAND,
        self::ADD_LEVEL_COMMAND,
        self::SUM_LEAF,
        self::STOP,
    ];

    const COMMAND_ARGUMENT_COUNT = [
        self::CREATE_COMMAND => 1,
        self::ADD_LEVEL_COMMAND => 2,
        self::SUM_LEAF => 1,
        self::STOP => 0,
    ];

    protected function configure()
    {
        $this
            ->setName('tree:init')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Welcome!');
        $inProgress = true;
        $treeList = [];
        $sumCache = [];
        do {
            try {
                $userCommand = $this->analyzeUserInput($input, $output);
            } catch (InvalidArgumentException $e) {
                $output->writeln($e->getMessage());
                continue;
            } catch (InvalidCommandException $e) {
                $output->writeln($e->getMessage());
                continue;
            }
            $command = $userCommand->getCommand();
            $args = $userCommand->getArguments();

            if ($command === self::CREATE_COMMAND) {

                $name = $args[0];
                $treeList[$name] = '';
                $output->writeln('Leaf ' . '<info>' . $name . '</info>' . ' is created');

            } else if ($command === self::ADD_LEVEL_COMMAND) {

                $name = $args[0];
                $level = $args[1];

                //First set must have only 1 leaf
                if (!array_key_exists($name, $treeList)) {
                    $output->writeln('You must create a leaf first');
                    continue;
                }

                $delim = '';
                if ($treeList[$name] !== '') {
                    $delim = ';';
                }
                $treeList[$name] .= $delim . $level;

                //Invalidate cache
                if (array_key_exists($name, $sumCache)) {
                    unset($sumCache[$name]);
                }

                $output->writeln($name . ' configuration now is: ' . $treeList[$name]);

            } else if ($command === self::SUM_LEAF) {

                $name = $args[0];

                $leafBuilder = new LeafBuilder();
                $leaf = null;
                try {
                    $leaf = $leafBuilder->buildTree($treeList[$name]);
                } catch (LeafParseValidationException $e) {
                    $output->writeln($e->getMessage() . ', add level again');
                    $treeList[$name] = '';
                }
                if ($leaf !== null) {
                    // Check sum cache
                    if (!array_key_exists($name, $sumCache)) {
                        $sumCache[$name] = $leaf->findMaxSum();
                    }
                    $maxSum = $sumCache[$name];
                    $output->writeln('Max sum is: ' . $maxSum);
                }

            } else if ($command === self::STOP) {
                $inProgress = false;
                $output->writeln('Goodbye...');
            }

        } while ($inProgress === true);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Subcommand
     * @throws InvalidArgumentException
     * @throws InvalidCommandException
     */
    private function analyzeUserInput(InputInterface $input, OutputInterface $output)
    {
        $userInput = $this->getHelper('question')->ask($input, $output, new Question(''));
        $userInput = explode(' ', $userInput, 2);

        $command = $userInput[0];
        $args = [];
        if (count($userInput) === 2) {
            $args = explode(' ', $userInput[1]);
        }
        if (!in_array($command, self::ALLOWED_COMMANDS)) {
            throw new InvalidCommandException('Unknown command');
        }
        if (self::COMMAND_ARGUMENT_COUNT[$command] !== count($args)) {
            throw new InvalidArgumentException('Incorrect number of arguments');
        }
        return new Subcommand($command, $args);
    }
}

class Subcommand {

    /** @var string */
    private $command;
    /** @var string[] */
    private $arguments;

    /**
     * Subcommand constructor.
     * @param string $command
     * @param string[] $arguments
     */
    public function __construct($command, $arguments)
    {
        $this->command = $command;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}