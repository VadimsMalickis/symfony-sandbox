<?php


namespace TreeBundle\TreeCalculator;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TreeBundle\TreeCalculator\Exception\LeafNotExistException;

class TreeCalculatorTest extends WebTestCase
{
    public function testCreateTree()
    {
        $calculator = new TreeCalculator();
        $calculator->createTree('task');
        $calculator->addLevel('task', 2);
        $this->assertEquals(2, $calculator->printTree('task'));

        $calculator->createTree('a');
        $calculator->addLevel('a', 23);
        $this->assertEquals(23, $calculator->printTree('a'));
    }

    public function testAddLevelException()
    {
        $calculator = new TreeCalculator();
        $this->expectException(LeafNotExistException::class);
        $calculator->addLevel('test', 2);
    }

    public function testFindMaxSum()
    {
        $calculator = new TreeCalculator();
        $calculator->createTree('task');
        $calculator->addLevel('task', 2);
        $calculator->addLevel('task', '2,11');
        $this->assertEquals(13, $calculator->calculateMaxSum('task'));
    }
}