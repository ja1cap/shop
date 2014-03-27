<?php

namespace Shop\OrderManagementBundle\Controller;

use Shop\CatalogBundle\Entity\CustomerOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminOrderController
 * @package Shop\OrderManagementBundle\Controller
 */
class AdminOrderController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ordersAction()
    {

        /**
         * @var $user \Shop\UserBundle\Entity\AbstractUser
         */
        $user = $this->getUser();

        /**
         * @var $orderRepository \Shop\CatalogBundle\Entity\CustomerOrderRepository
         */
        $orderRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:CustomerOrder');

        $orders = $orderRepository->findOrders($user);

        return $this->render('ShopOrderManagementBundle:AdminOrder:orders.html.twig', array(
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

        return $this->render('ShopOrderManagementBundle:AdminOrder:order.html.twig', array(
            'order' => $order,
            'revisions' => $revisions,
        ));

    }

    public function orderRevisions($id){

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

            var_dump($revision->getUsername());
            var_dump($revision->getTimestamp());

            $customerOrderAudit = $auditReader->find('Shop\CatalogBundle\Entity\CustomerOrder', $order->getId(), $revision->getRev());

            if($customerOrderAudit instanceof CustomerOrder){

                var_dump($customerOrderAudit->getId());
                var_dump($customerOrderAudit->getCustomerName());
                var_dump($customerOrderAudit->getCustomerEmail());
                var_dump($customerOrderAudit->getCustomerPhone());
                var_dump($customerOrderAudit->getCustomerComment());
                var_dump($customerOrderAudit->getStatus());

                foreach($customerOrderAudit->getCurrentSerializedProposals() as $serializedProposal){

                    var_dump(unserialize($serializedProposal));

                }

            }

        }

        die;

        return $this->render('ShopOrderManagementBundle:AdminOrder:order.html.twig', array(
            'order' => $order
        ));

    }

}
