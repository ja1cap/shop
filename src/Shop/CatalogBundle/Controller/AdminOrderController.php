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

        /**
         * @var $auditReader \SimpleThings\EntityAudit\AuditReader
         */
        $auditReader = $this->get("simplethings_entityaudit.reader");
        $revisions = $auditReader->findRevisions('Shop\CatalogBundle\Entity\CustomerOrder', $order->getId());

        /**
         * @var $revision \SimpleThings\EntityAudit\Revision
         */
        foreach($revisions as $revision){
            $customerOrderAudit = $auditReader->find('Shop\CatalogBundle\Entity\CustomerOrder', $order->getId(), $revision->getRev());
            if($customerOrderAudit instanceof CustomerOrder){
                var_dump($customerOrderAudit->getProposals()->toArray());
            }
        }

        return $this->render('ShopCatalogBundle:AdminOrder:order.html.twig', array(
            'order' => $order
        ));

    }

}
