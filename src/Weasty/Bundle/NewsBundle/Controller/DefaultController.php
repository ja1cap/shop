<?php

namespace Weasty\Bundle\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WeastyNewsBundle:Default:index.html.twig', array('name' => $name));
    }
}
