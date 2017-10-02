<?php

namespace TreeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/tree")
     */
    public function indexAction()
    {
        return $this->render('TreeBundle:Default:index.html.twig');
    }
}
