<?php

namespace Shop\DiscountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ShopDiscountBundle:Default:index.html.twig', array('name' => $name));
    }
}
