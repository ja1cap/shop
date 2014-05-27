<?php

namespace Shop\MainBundle\Controller;

use Shop\CatalogBundle\Entity\Action;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\MainBundle\Entity\Address;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Shop\MainBundle\Controller
 */
class DefaultController extends Controller
{

    public function indexAction()
    {

        $request_form = $this->createFormBuilder(null, array(
                'attr' => array(
                    'class' => 'request-form form-box',
                ),
            ))
            ->add('request_customer_name', 'text', array(
                'label' => 'КАК К ВАМ ОБРАЩАТЬСЯ*',
                'required' => true,
                'attr' => array(
                    'class' => 'customer-name',
                ),
            ))
            ->add('request_customer_phone', 'text', array(
                'label' => 'ТЕЛЕФОН*',
                'attr' => array(
                    'class' => 'customer-phone',
                ),
            ))
            ->add('request_customer_email', 'text', array(
                'label' => 'E-MAIL',
                'attr' => array(
                    'class' => 'customer-email',
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'ОТПРАВИТЬ ЗАЯВКУ',
                'attr' => array(
                    'class' => 'submit-btn',
                ),
            ))
            ->getForm();

//        $footer_request_form = $this->createFormBuilder(null, array(
//                'attr' => array(
//                    'class' => 'request-form form-box',
//                ),
//            ))
//            ->add('footer_request_customer_name', 'text', array(
//                'label' => 'КАК К ВАМ ОБРАЩАТЬСЯ*',
//                'required' => true,
//                'attr' => array(
//                    'class' => 'customer-name',
//                ),
//            ))
//            ->add('footer_request_customer_phone', 'text', array(
//                'label' => 'ТЕЛЕФОН*',
//                'attr' => array(
//                    'class' => 'customer-phone',
//                ),
//            ))
//            ->add('footer_request_customer_email', 'text', array(
//                'label' => 'E-MAIL',
//                'attr' => array(
//                    'class' => 'customer-email',
//                ),
//            ))
//            ->add('save', 'submit', array(
//                'label' => 'ОТПРАВИТЬ ЗАЯВКУ',
//                'attr' => array(
//                    'class' => 'submit-btn',
//                ),
//            ))
//            ->getForm();

        return $this->render('ShopMainBundle:Default:index.html.twig', array(
            'request_form' => $request_form->createView(),
//            'footer_request_form' => $footer_request_form->createView(),
//            'why_us_items' => $this->getWhyUsItems(),
            'benefits' => $this->getBenefits(),
            'actions' => $this->getActions(),
            'reviews' => $this->getReviews(),
//            'how_we_items' => $this->getHowWeItems(),
//            'problems' => $this->getProblems(),
//            'solutions' => $this->getSolutions(),
            'addresses' => $this->getAddresses(),
        ));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function processRequestAction(Request $request){

        $this->sendEmail($request);
        return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderProposalAction(Request $request){

        $this->sendEmail($request);
        return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function callbackAction(Request $request){

        if($request->getMethod() == 'POST'){

            $this->sendEmail($request);
            return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

        } else{

            return $this->render('ShopMainBundle:Default:callback.html.twig');

        }

    }

    protected function sendEmail(Request $request){

        /**
         * @var $settings \Shop\MainBundle\Entity\Settings
         * @var $contacts \Shop\MainBundle\Data\ShopContactsResource
         */
        $settings = $this->get('shop_main.settings.resource')->getSettings();
        $contacts = $this->get('shop_main.contacts.resource');

        $proposal_information = null;
        $proposal_name = $request->get('name');
        $proposal_price = $request->get('price');

        if($proposal_name && $proposal_price){
            $proposal_information = $proposal_name . ', ' . $proposal_price;
        }

        $twig = clone $this->get('twig');
        $twig->setLoader(new \Twig_Loader_String());
        $data = array(
            'customer_name' => $request->get('customer_name'),
            'customer_phone' => $request->get('customer_phone'),
            'customer_email' => $request->get('customer_email'),
            'customer_comment' => $request->get('comment'),
            'order_information' => $proposal_information,
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
     * @return array
     */
    protected function getActions(){
        return $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action')->findBy(
            array(
                'status' => Action::STATUS_ON,
            ),
            array(
                'position' => 'ASC',
            )
        );
    }

    /**
     * @return array
     */
    protected function getProblems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Problem')->findAll();
    }

    /**
     * @return array
     */
    protected function getSolutions(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Solution')->findAll();
    }

    /**
     * @return array
     */
    protected function getWhyUsItems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:WhyUsItem')->findAll();
    }

    /**
     * @return array
     */
    protected function getHowWeItems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HowWeItem')->findAll();
    }

    /**
     * @return array
     */
    protected function getReviews(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Review')->findAll();
    }

    /**
     * @return array
     */
    protected function getBenefits(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Benefit')->findAll();
    }

    /**
     * @return array
     */
    protected function getAddresses(){
        $addresses = array();
        $entities = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findAll();
        foreach($entities as $entity){
            if($entity instanceof Address){
                $addresses[] = array(
                    'name' => $entity->getName(),
                    'description' => $entity->getDescription(),
                    'phones' => $entity->getPhones(),
                    'work_schedule' => $entity->getWorkSchedule(),
                    'latitude' => $entity->getLatitude(),
                    'longitude' => $entity->getLongitude(),
                );
            }
        }
        return $addresses;
    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getProposalRepository()->findOneBy($criteria);

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        $category = $proposal->getCategory();

        $filtersBuilder = $this->get('shop_catalog.category.filters.builder');
        $filtersResource = $filtersBuilder->buildFromRequest($category, $proposal, $request);

        $priceData = $this->getProposalRepository()->findProposalPrice(
            $category->getId(),
            $proposal->getId(),
            $filtersResource
        );
        $price = $priceData ? $priceData['priceEntity'] : null;

        $proposalFeatures = array();

        if($price instanceof Price){

            foreach($category->getParameters() as $categoryParameter){
                if($categoryParameter instanceof CategoryParameter){
                    $proposalFeatures[$categoryParameter->getParameterId()] = null;
                }
            }

            foreach($price->getParameterValues() as $parameterValue){
                if($parameterValue instanceof ParameterValue){
                    if(array_key_exists($parameterValue->getParameterId(), $proposalFeatures)){
                        $proposalFeatures[$parameterValue->getParameterId()] = $parameterValue;
                    }
                }
            }

        }

        $shopCart = $this->buildShopCart($request);

        $actions = array();

        $shippingCalculatorResult = null;

        if($price instanceof Price){

            $shopCartSummaryPrice = $shopCart['summaryPrice'];
            $shopCartPricesIds = $shopCart['priceIds'];

            if(!in_array($price->getId(), $shopCartPricesIds)){
                $possibleSummaryPrice = ($shopCartSummaryPrice + $this->get('shop_catalog.price.currency.converter')->convert($price));
            } else {
                $possibleSummaryPrice = $shopCartSummaryPrice;
            }

            $shopCartCategoryIds = $shopCart['categoryIds'];
            $possibleCategoryIds = array_unique(array_merge($shopCartCategoryIds, array($category->getId())));

            if($possibleSummaryPrice && $possibleCategoryIds){

                /**
                 * @var $actionRepository \Shop\CatalogBundle\Entity\ActionRepository
                 */
                $actionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action');
                $actions = $actionRepository->findActions($possibleCategoryIds, $possibleSummaryPrice);

            }

            $customerCity = null;
            if($request->get('customerCity')){
                $customerCity = $this->get('weasty_geonames.city.repository')->findOneBy(array(
                    'id' => (int)$request->get('customerCity'),
                ));
            }
            $customerCity = $customerCity ?: $this->get('weasty_geonames.city.locator')->getCity();
            $customerLiftType = $request->get('customerLiftType', ShippingLiftingPrice::LIFT_TYPE_LIFT);
            $customerFloor = $request->get('customerFloor', 10);

            $shippingCalculatorResult = $this->get('shop_shipping.shipping_calculator')->calculate(array(
                'shopCartCategories' => array($category),
                'shopCartSummaryPrice' => $this->get('shop_catalog.price.currency.converter')->convert($price),
                'city' => $customerCity,
                'liftType' => $customerLiftType,
                'floor' => $customerFloor,
            ));

        }
        $priceData['shipping'] = $shippingCalculatorResult;

        $additionalCategoriesData = $this->getProposalAdditionalCategories($category, $request);

        $response = $this->render('ShopMainBundle:Default:proposal.html.twig', array(
            'category' => $category,
            'additionalCategoriesData' => $additionalCategoriesData,
            'proposal' => $proposal,
            'price' => $price,
            'priceData' => $priceData,
            'proposalFeatures' => array_filter($proposalFeatures),
            'actions' => $actions,
            'shopCart' => $shopCart,
            'filtersResource' => $filtersResource,
        ));
        $filtersBuilder->setFiltersCookies($category, $request, $response);

        return $response;

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
     * @return array
     * @throws \InvalidArgumentException
     */
    public function buildShopCart(Request $request)
    {
        $shopCartStorageData = json_decode($request->cookies->get('shopCart'), true);
        return $this->getShopCartFactory()->buildShopCart($shopCartStorageData);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return array
     */
    protected function getProposalAdditionalCategories(Category $category, Request $request)
    {

        $additionalCategoriesData = array();

        /**
         * @var $additionalCategory Category
         */
        foreach ($category->getAdditionalCategories() as $additionalCategory) {

            $filtersBuilder = $this->get('shop_catalog.category.filters.builder');
            $filtersResource = $filtersBuilder->buildFromRequest($additionalCategory, null, $request);

            $additionalCategoryProposals = $this->getProposalRepository()->findProposalsByParameters($additionalCategory->getId(), $filtersResource, 1, 1);
            if ($additionalCategoryProposals) {

                $additionalCategoriesData[$additionalCategory->getId()] = array(
                    'category' => $additionalCategory,
                    'proposalData' => current($additionalCategoryProposals),
                );

            }

        }

        return $additionalCategoriesData;

    }

    /**
     * @return \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected function getProposalRepository()
    {
        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        return $proposalRepository;
    }

}
