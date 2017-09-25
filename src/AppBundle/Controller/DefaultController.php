<?php

namespace AppBundle\Controller;

use AppBundle\BracketCheck\BracketChecker;
use AppBundle\Tree\LeafBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $builder = new LeafBuilder();
//        $tree = $builder->buildTree("1,2,3;4,5;6,7;8,9;10,11,12,13,14");
//
//        return new Response($tree->toJSON() . "\nMax value: " . $tree->findMax());

        $builder = new LeafBuilder();

        $tree = $builder->buildTree('1;2,3;;4,5');

        return new Response('');
    }
}
