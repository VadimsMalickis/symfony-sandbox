<?php


namespace AppBundle\Tree;


use AppBundle\Tree\Exception\LeafParseValidationException;
use AppBundle\Tree\Exception\LeafParseValidationExceptionBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeafBuilderTest extends WebTestCase
{
    public function testBuildTree()
    {
        $builder = new LeafBuilder();

        $tree = $builder->buildTree('1');
        $this->assertEquals(1, $tree->getValue());
        $this->assertEquals(0, count($tree->getChilds()));

        $tree = $builder->buildTree('1;2;3');
        $this->assertEquals(1, $tree->getValue());
        $this->assertEquals(1, count($tree->getChilds()));
        $this->assertEquals(1, count($tree->getChilds()[0]->getChilds()));
        $this->assertEquals(3, $tree->getChilds()[0]->getChilds()[0]->getValue());

        $tree = $builder->buildTree('3;2,5,4;1,2;3,7');
        $this->assertEquals(3, $tree->getValue());
        $this->assertEquals(3, count($tree->getChilds()));
        $this->assertEquals(2, $tree->getChilds()[0]->getValue());
        $this->assertEquals(5, $tree->getChilds()[1]->getValue());
        $this->assertEquals(4, $tree->getChilds()[2]->getValue());

        $this->assertEquals(2, count($tree->getChilds()[0]->getChilds()));
        $this->assertEquals(1, $tree->getChilds()[0]->getChilds()[0]->getValue());
        $this->assertEquals(2, $tree->getChilds()[0]->getChilds()[1]->getValue());

        $this->assertEquals(2, count($tree->getChilds()[1]->getChilds()));
        $this->assertEquals(3, $tree->getChilds()[1]->getChilds()[0]->getValue());
        $this->assertEquals(7, $tree->getChilds()[1]->getChilds()[1]->getValue());

        $this->assertEquals(0, count($tree->getChilds()[2]->getChilds()));
        $this->assertEquals(0, count($tree->getChilds()[0]->getChilds()[0]->getChilds()));
        $this->assertEquals(0, count($tree->getChilds()[0]->getChilds()[1]->getChilds()));

        $tree = $builder->buildTree('-1');
        $this->assertEquals(-1, $tree->getValue());
        $this->assertEquals(0, count($tree->getChilds()));

        $tree = $builder->buildTree('-1;-2;-3');
        $this->assertEquals(-1, $tree->getValue());
        $this->assertEquals(1, count($tree->getChilds()));
        $this->assertEquals(1, count($tree->getChilds()[0]->getChilds()));
        $this->assertEquals(-3, $tree->getChilds()[0]->getChilds()[0]->getValue());

        $tree = $builder->buildTree('1;2,3;;4');
        $this->assertEquals(4, $tree->getChilds()[1]->getChilds()[0]->getValue());
        $this->assertEquals(1, count($tree->getChilds()[1]->getChilds()));
        $this->assertEquals(0, count($tree->getChilds()[0]->getChilds()));
        $this->assertEquals(4, $tree->getChilds()[1]->getChilds()[0]->getValue());

        $tree = $builder->buildTree('1;-2,-33;;4');
        $this->assertEquals(-2, $tree->getChilds()[0]->getValue());
        $this->assertEquals(-33, $tree->getChilds()[1]->getValue());
        $this->assertEquals(4, $tree->getChilds()[1]->getChilds()[0]->getValue());
    }

    public function testParseValidationException_illegalCharInTheBeginning()
    {
        $builder = new LeafBuilder();

        $expectedException = (new LeafParseValidationExceptionBuilder())
            ->setIllegalCharAtPosition()
            ->setChar('a')
            ->setPosition(0)
            ->build();

        $this->expectException(LeafParseValidationException::class);
        $this->expectExceptionMessage($expectedException->getMessage());
        $builder->buildTree('a1;-2;-5;3,3');
    }

    public function testParseValidationException_illegalCharInTheMiddle()
    {
        $builder = new LeafBuilder();

        $expectedException = (new LeafParseValidationExceptionBuilder())
            ->setIllegalCharAtPosition()
            ->setChar('x')
            ->setPosition(6)
            ->build();

        $this->expectException(LeafParseValidationException::class);
        $this->expectExceptionMessage($expectedException->getMessage());
        $builder->buildTree('-1;-2;x5;3,3');
    }

    public function testParseValidationException_illegalCharInTheEnd()
    {
        $builder = new LeafBuilder();

        $expectedException = (new LeafParseValidationExceptionBuilder())
            ->setIllegalCharAtPosition()
            ->setChar('*')
            ->setPosition(12)
            ->build();

        $this->expectException(LeafParseValidationException::class);
        $this->expectExceptionMessage($expectedException->getMessage());
        $builder->buildTree('-1;-2;-5;3,3*');
    }

    public function testParseValidationException_duplicateSpecialCharAtPosition ()
    {
        $builder = new LeafBuilder();

        $expectedException =  (new LeafParseValidationExceptionBuilder())
            ->setIllegalCharAtPosition()
            ->setChar('-')
            ->setPosition(1)
            ->build();
        $this->expectException(LeafParseValidationException::class);
        $this->expectExceptionMessage($expectedException->getMessage());
        $builder->buildTree('--1;-2;55;3,3');
    }

    public function testSkipWhitespaces()
    {
        $builder = new LeafBuilder();
        $tree = $builder->buildTree('  3 ;  2,  5 , 4 ; 1 ,2 ;3 , 7 ');
        $this->assertEquals(3, $tree->getValue());
        $this->assertEquals(3, count($tree->getChilds()));
        $this->assertEquals(2, $tree->getChilds()[0]->getValue());
        $this->assertEquals(5, $tree->getChilds()[1]->getValue());
        $this->assertEquals(4, $tree->getChilds()[2]->getValue());
    }

    public function testFirstLeaf()
    {
        $builder = new LeafBuilder();

        $expectedException =  (new LeafParseValidationExceptionBuilder())
            ->setIllegalCharAtPosition()
            ->setChar(',')
            ->setPosition(1)
            ->build();
        $this->expectException(LeafParseValidationException::class);
        $this->expectExceptionMessage($expectedException->getMessage());

        $builder->buildTree('1,2;2;55;3,3');
    }

    public function testChildSetDelimiter()
    {
        $builder = new LeafBuilder();

        $this->expectException(LeafParseValidationException::class);
        $builder->buildTree('1;2,7;;;5,8');
    }
}