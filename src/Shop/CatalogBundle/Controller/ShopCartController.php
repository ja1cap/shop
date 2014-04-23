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
     * @var ShopCart
     */
    private $shopCart;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request)
    {

        $shopCartSummary = $this->getShopCartSummary($request);

        $actions = array();
        $shopCartSummaryPrice = $shopCartSummary['summaryPrice'];
        $shopCartCategoriesData = $shopCartSummary['categories'];
        $shopCartCategoryIds = $shopCartSummary['categoryIds'];

        if($shopCartSummaryPrice && $shopCartCategoryIds){

            /**
             * @var $actionRepository \Shop\CatalogBundle\Entity\ActionRepository
             */
            $actionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action');
            $actions = $actionRepository->findActions($shopCartCategoryIds, $shopCartSummaryPrice);

        }

        $shippingMethods = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod')->findBy(array(), array(
            'name' => 'ASC',
        ));

        $cities = $this->get('weasty_geonames.city.repository')->getCountryCities();

        $customerCity = null;
        if($request->get('customerCity')){
            $customerCity = $this->get('weasty_geonames.city.repository')->findOneBy(array(
                'id' => (int)$request->get('customerCity'),
            ));
        }
        $customerCity = $customerCity ?: $this->get('weasty_geonames.city.locator')->getCity();

        $shippingSummaries =$this->get('shop_shipping.shipping_calculator')->calculate(array(
            'orderCategories' => $shopCartCategoriesData,
            'orderSummaryPrice' => $shopCartSummaryPrice,
            'city' => $customerCity,
            'liftType' => $request->get('liftType'),
            'floor' => $request->get('floor', 10),
        ));

        return $this->render('ShopCatalogBundle:ShopCart:default.html.twig', array(
            'title' => 'Оформление заказа',
            'customerCity' => $customerCity,
            'cities' => $cities,
            'actions' => $actions,
            'shippingMethods' => $shippingMethods,
            'shippingSummaries' => $shippingSummaries,
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

            $shopCartSummary = $this->getShopCartSummary($request);

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
                                        ->setPriceValue($this->get('shop_catalog.price.currency.converter')->convert($price))
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
     * @return ShopCart
     */
    protected function getShopCart()
    {

        if(!$this->shopCart instanceof ShopCart){

            $proposalsRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
            $categoryRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
            $priceRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price');
            $currencyConverter = $this->get('shop_catalog.price.currency.converter');

            $this->shopCart = new ShopCart($currencyConverter, $categoryRepository, $proposalsRepository, $priceRepository);

        }

        return $this->shopCart;

    }

    /**
     * @param Request $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getShopCartSummary(Request $request)
    {
        $shopCartStorageData = json_decode($request->cookies->get('shopCart'), true);
        $shopCartSummary = $this->getShopCart()->getSummary($shopCartStorageData);
        return $shopCartSummary;
    }

}
