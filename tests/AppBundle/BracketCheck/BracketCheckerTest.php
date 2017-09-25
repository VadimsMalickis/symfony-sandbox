<?php


namespace AppBundle\BracketCheck;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BracketCheckerTest extends WebTestCase
{

    public function testBracketCheck()
    {
        $checker = new BracketChecker();

        $this->assertTrue($checker->check('()'));
        $this->assertTrue($checker->check('([])'));
        $this->assertTrue($checker->check('({[]})'));
        $this->assertTrue($checker->check('({()[]}{()}[[]])'));
        $this->assertTrue($checker->check('da43{43}434{}'));

        $this->assertFalse($checker->check('('));
        $this->assertFalse($checker->check(')'));
        $this->assertFalse($checker->check('[}'));
        $this->assertFalse($checker->check('[]]'));
        $this->assertFalse($checker->check('([)]'));
        $this->assertFalse($checker->check('({['));
        $this->assertFalse($checker->check('({[]})('));
        $this->assertFalse($checker->check('j{[{oklmn'));
    }

}