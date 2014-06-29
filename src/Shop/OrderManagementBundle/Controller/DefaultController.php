<?php

namespace Shop\OrderManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ShopOrderManagementBundle:Default:index.html.twig', array('name' => $name));
    }
}
