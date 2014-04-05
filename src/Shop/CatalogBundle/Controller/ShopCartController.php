<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Cart\ShopCart;
use Shop\CatalogBundle\Entity\Action;
use Shop\CatalogBundle\Entity\CustomerOrder;
use Shop\CatalogBundle\Entity\CustomerOrderProposal;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\MainBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShopCartController
 * @package Shop\CatalogBundle\Controller
 */
class ShopCartController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request)
    {

        $shopCartSummary = $this->getShopCart($request)->getSummary();

        $actions = array();
        $shopCartSummaryPrice = $shopCartSummary['summaryPrice'];
        $shopCartCategoriesIds = $shopCartSummary['categoriesIds'];

        if($shopCartSummaryPrice && $shopCartCategoriesIds){

            /**
             * @var $actionRepository \Shop\CatalogBundle\Entity\ActionRepository
             */
            $actionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action');
            $actions = $actionRepository->findActions($shopCartCategoriesIds, $shopCartSummaryPrice);

        }

        $shippingMethods = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopCatalogBundle:ShopCart:default.html.twig', array(
            'title' => 'Оформление заказа',
            'actions' => $actions,
            'shippingMethods' => $shippingMethods,
            'shopCartSummary' => $shopCartSummary,
        ));

    }

    public function orderAction(Request $request){

        $customerName = $request->get('customerName');
        $customerPhone = $request->get('customerPhone');
        $customerEmail = $request->get('customerEmail') ?: null;
        $customerComment = $request->get('customerComment') ?: null;

        if($customerName && $customerPhone){

            $action = null;
            $actionId = $request->get('actionId');

            if($actionId){
                $action = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action')->findOneBy(array(
                    'id' => (int)$actionId,
                ));
            }

            $orderInformation = null;
            $shopCartSummary = $this->getShopCart($request)->getSummary();
            if($shopCartSummary['categories']){

                $customerOrder = new CustomerOrder();
                $customerOrder
                    ->setStatus(CustomerOrder::STATUS_NEW)
                    ->setCustomerName($customerName)
                    ->setCustomerPhone($customerPhone)
                    ->setCustomerEmail($customerEmail)
                    ->setCustomerComment($customerComment)
                    ->setCreateDate(new \DateTime());

                if($action instanceof Action){
                    $customerOrder->setAction($action);
                }

                foreach($shopCartSummary['categories'] as $categoryData){

                    $proposals = $categoryData['proposals'];
                    foreach($proposals as $proposalData){

                        $proposal = $proposalData['proposal'];
                        $prices = $proposalData['prices'];

                        if($proposal instanceof Proposal){

                            foreach($prices as $priceData){

                                $price = $priceData['price'];
                                $amount = $priceData['amount'];

                                if($price instanceof Price){

                                    $orderProposal = new CustomerOrderProposal();
                                    $orderProposal
                                        ->setProposal($proposal)
                                        ->setPrice($price)
                                        ->setAmount($amount);

                                    $customerOrder->addProposal($orderProposal);

                                }

                            }

                        }

                    }

                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($customerOrder);
                $em->flush();

                $orderInformation = $this->renderView('ShopCatalogBundle:ShopCart:orderInformation.html.twig', array(
                    'action' => $action,
                    'shopCartSummary' => $shopCartSummary,
                ));

            }

//            $this->sendEmail($customerName, $customerPhone, $customerEmail, $customerComment, $orderInformation);

            $response = $this->render('ShopCatalogBundle:ShopCart:order.html.twig', array(
                'title' => 'Заказ оформлен',
            ));

            $response->headers->clearCookie('shopCart');

            return $response;

        } else {

            return $this->redirect($this->generateUrl('shop_cart'));

        }

    }

    /**
     * @param $customerName
     * @param $customerPhone
     * @param $customerEmail
     * @param $customerComment
     * @param $orderInformation
     */
    protected function sendEmail($customerName, $customerPhone, $customerEmail, $customerComment, $orderInformation){

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        /**
         * @var $twig \Twig_Environment
         */
        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $data = array(
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_email' => $customerEmail,
            'customer_comment' => $customerComment,
            'order_information' => $orderInformation,
            'shop_name' => $settings->getName(),
            'shop_address' => $settings->getAddress(),
            'shop_phone' => $settings->getPhone(),
            'shop_email' => $settings->getEmail(),
        );

        if($data['customer_email']){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($data['customer_email'])
                ->setBody($twig->render($settings->getCustomerEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

        if($settings->getManagerEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getManagerEmail())
                ->setBody($twig->render($settings->getManagerEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

        if($settings->getAdminEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getAdminEmail())
                ->setBody($twig->render($settings->getAdminEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

    }

    /**
     * @param Request $request
     * @return ShopCart
     */
    protected function getShopCart(Request $request)
    {
        $shopCartStorageData = json_decode($request->cookies->get('shopCart'), true);
        $proposalsRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $categoryRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
        $priceRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price');
        $shopCart = new ShopCart($categoryRepository, $proposalsRepository, $priceRepository, $shopCartStorageData);
        return $shopCart;
    }

}
