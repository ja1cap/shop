<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\CustomerOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminOrderController
 * @package Shop\CatalogBundle\Controller
 */
class AdminOrderController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ordersAction()
    {

        $orders = $this->getDoctrine()->getRepository('ShopCatalogBundle:CustomerOrder')->findBy(
            array(),
            array(
                'createDate' => 'DESC'
            )
        );

        return $this->render('ShopCatalogBundle:AdminOrder:orders.html.twig', array(
            'orders' => $orders
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function orderAction($id, Request $request)
    {

        $order = $this->getDoctrine()->getRepository('ShopCatalogBundle:CustomerOrder')->findOneBy(array(
            'id' => $id,
        ));

        if(!$order instanceof CustomerOrder){
            throw $this->createNotFoundException('Заказ не найден');
        }

        $order->setStatus(CustomerOrder::STATUS_ACCEPTED);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('ShopCatalogBundle:AdminOrder:order.html.twig', array(
            'order' => $order
        ));

    }

}
