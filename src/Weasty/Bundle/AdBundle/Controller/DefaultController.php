<?php

namespace Weasty\Bundle\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WeastyAdBundle:Default:index.html.twig', array('name' => $name));
    }
}
