<?php

namespace Shop\ShippingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ShopShippingBundle:Default:index.html.twig', array('name' => $name));
    }
}
