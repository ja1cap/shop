<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Action;
use Shop\CatalogBundle\Entity\CustomerOrder;
use Shop\CatalogBundle\Entity\CustomerOrderProposal;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
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

        $shopCart = $this->buildShopCart($request);

        $actions = array();
        $shopCartSummaryPrice = $shopCart->getSummaryPrice();
        $shopCartCategoryIds = $shopCart->getCategoryIds();

        if($shopCartSummaryPrice && $shopCartCategoryIds){

            /**
             * @var $actionRepository \Shop\CatalogBundle\Entity\ActionRepository
             */
            $actionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action');
            $actions = $actionRepository->findActions($shopCartCategoryIds, $shopCartSummaryPrice);

        }

        $cities = $this->get('weasty_geonames.city.repository')->getCountryCities();
        if($request->get('customerCity')){
            $customerCity = $this->get('weasty_geonames.city.repository')->findOneBy(array(
                'id' => (int)$request->get('customerCity'),
            ));
            $shopCart->setCustomerCity($customerCity);
        }

        $customerLiftType = $request->get('liftType', ShippingLiftingPrice::LIFT_TYPE_LIFT);
        $customerFloor = $request->get('floor', 10);

        $shopCart
            ->setCustomerLiftType($customerLiftType)
            ->setCustomerFloor($customerFloor)
        ;

        return $this->render('ShopCatalogBundle:ShopCart:default.html.twig', array(
            'title' => 'Оформление заказа',
            'cities' => $cities,
            'actions' => $actions,
            'shopCart' => $shopCart,
        ));

    }

    public function orderAction(Request $request){

        $customerName = $request->get('customerName');
        $customerPhone = $request->get('customerPhone');
        $customerEmail = $request->get('customerEmail') ?: null;
        $customerComment = $request->get('customerComment') ?: null;

        $customerCity = null;
        if($request->get('customerCity')){
            $customerCity = $this->get('weasty_geonames.city.repository')->findOneBy(array(
                'id' => (int)$request->get('customerCity'),
            ));
        }
        $customerCity = $customerCity ?: $this->get('weasty_geonames.city.locator')->getCity();
        $customerLiftType = $request->get('customerLiftType', ShippingLiftingPrice::LIFT_TYPE_LIFT);
        $customerFloor = $request->get('customerFloor', 10);

        if($customerName && $customerPhone){

            $action = null;
            $actionId = $request->get('actionId');

            if($actionId){
                $action = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action')->findOneBy(array(
                    'id' => (int)$actionId,
                ));
            }

            $orderInformation = null;

            $shopCart = $this->buildShopCart($request);

            $shippingCalculatorResult = $this->get('shop_shipping.shipping_calculator')->calculate(array(
                'shopCartCategories' => $shopCart->getCategories(),
                'shopCartSummaryPrice' => $shopCart->getSummaryPrice(),
                'city' => $customerCity,
                'liftType' => $customerLiftType,
                'floor' => $customerFloor,
            ));

            $shopCart->setShippingCalculatorResult($shippingCalculatorResult);

            if($shopCart['categories']){

                $customerOrder = new CustomerOrder();
                $customerOrder
                    ->setCustomerName($customerName)
                    ->setCustomerPhone($customerPhone)
                    ->setCustomerEmail($customerEmail)
                    ->setCustomerComment($customerComment)
                    ->setCreateDate(new \DateTime())
                ;

                if($action instanceof Action){
                    $customerOrder->setAction($action);
                }

                foreach($shopCart['categories'] as $categoryData){

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
                                        ->setAmount($amount)
                                    ;

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
                    'shopCart' => $shopCart,
                ));

            }

            $this->sendEmail($customerName, $customerPhone, $customerEmail, $customerComment, $orderInformation);

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

        /**
         * @var $settings \Shop\MainBundle\Entity\Settings
         * @var $contacts \Shop\MainBundle\Data\ShopContactsResource
         */
        $settings = $this->get('shop_main.settings.resource')->getSettings();
        $contacts = $this->get('shop_main.contacts.resource');

        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $data = array(
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_email' => $customerEmail,
            'customer_comment' => $customerComment,
            'order_information' => $orderInformation,
            'shop_name' => $settings->getName(),
            'shop_address' => $settings->getAddress(),
            'shop_phone' => implode(', ', $contacts->getPhones()),
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
     * @return \Shop\CatalogBundle\Cart\ShopCartFactory
     */
    protected function getShopCartFactory()
    {
        return $this->get('shop_catalog.shop_cart.factory');

    }

    /**
     * @param Request $request
     * @return \Shop\CatalogBundle\Cart\ShopCart
     * @throws \InvalidArgumentException
     */
    public function buildShopCart(Request $request)
    {
        $shopCartStorageData = json_decode($request->cookies->get('shopCart'), true);
        $shopCart = $this->getShopCartFactory()->buildShopCart($shopCartStorageData);
        return $shopCart;
    }

}
