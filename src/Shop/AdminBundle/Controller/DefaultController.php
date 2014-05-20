<?php

namespace Shop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package Shop\AdminBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ShopAdminBundle:Default:index.html.twig');
    }

}
