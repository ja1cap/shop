<?php

namespace Shop\MainBundle\Controller;

use Shop\CatalogBundle\Cart\ShopCart;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
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
            'settings' => $this->getSettings(),
            'request_form' => $request_form->createView(),
//            'footer_request_form' => $footer_request_form->createView(),
//            'why_us_items' => $this->getWhyUsItems(),
            'benefits' => $this->getBenefits(),
            'categories' => $this->getCategories(),
            'proposals' => $this->getProposals(),
            'actions' => $this->getActions(),
            'reviews' => $this->getReviews(),
//            'images' => $this->getImages(),
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

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

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
            'shop_phone' => $settings->getPhone(),
            'shop_email' => $settings->getEmail(),
        );

        if($data['customer_email']){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($data['customer_email'])
                ->setBody($twig->render($settings->getCustomerEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

        if($settings->getManagerEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getManagerEmail())
                ->setBody($twig->render($settings->getManagerEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

        if($settings->getAdminEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getAdminEmail())
                ->setBody($twig->render($settings->getAdminEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

    }

    /**
     * @return array
     */
    protected function getActions(){
        return $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action')->findBy(array(), array(
            'position' => 'ASC'
        ));
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
    protected function getImages(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Image')->findAll();
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
     * @return array
     */
    protected function getProposals(){

        $proposals = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Proposal')->findBy(array(
            'showOnHomePage' => true
        ));

        return $proposals;

    }

    /**
     * @return array
     */
    protected function getCategories(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findBy(array(), array(
            'name' => 'ASC',
        ));
    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($slug, Request $request){

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
            'slug' => $slug,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('shop'));
        }

        list($filteredParameterCookieName, $filteredParameterValues) = $this->getFilteredParameterValues($request);

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');

        $manufacturer = $request->get('manufacturer', $request->cookies->get('manufacturer'));
        $manufacturers = $proposalRepository->findCategoryManufacturers($category->getId());
        $manufacturerIds = array_map(function(Manufacturer $manufacturer){
            return $manufacturer->getId();
        }, $manufacturers);
        if(!in_array($manufacturer, $manufacturerIds)){
            $manufacturer = null;
        }

        $parametersOptions = $proposalRepository->findCategoryParametersOptions($category->getId());
        list($parametersData, $parametersOptionsIds) = $this->buildParametersData($parametersOptions);

        $extraParametersData = array_filter(
            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use (&$parametersData) {

                if(!$categoryParameter->getIsMain() && isset($parametersData[$categoryParameter->getParameterId()])){

                    $parameterData = $parametersData[$categoryParameter->getParameterId()];
                    unset($parametersData[$categoryParameter->getParameterId()]);
                    return $parameterData;

                }

                return false;

            })->toArray()
        );

        $proposals = $proposalRepository->findProposals(
            $category->getId(),
            $manufacturer,
            array_filter($filteredParameterValues, function($parameterOptionId) use ($parametersOptionsIds) {
                return in_array($parameterOptionId, $parametersOptionsIds);
            })
        );

        $viewParameters = array(
            'shopCartSummary' => $this->getShopCart($request)->getSummary(),
            'category' => $category,
            'categories' => $this->getCategories(),
            'proposals' => $proposals,
            'settings' => $this->getSettings(),
            'manufacturers' => $manufacturers,
            'parametersData' => $parametersData,
            'extraParametersData' => $extraParametersData,
            'filteredManufacturer' => $manufacturer,
            'filteredParameterValues' => $filteredParameterValues,
        );

        $response = $this->render('ShopMainBundle:Default:proposals.html.twig', $viewParameters);
        $response->headers->setCookie(new Cookie($filteredParameterCookieName, json_encode($filteredParameterValues)));

        if($request->query->has('manufacturer')){
            $response->headers->setCookie(new Cookie('manufacturer', $manufacturer));
        }

        return $response;

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

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $proposal = $proposalRepository->findOneBy($criteria);

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        $parametersOptions = $proposalRepository->findProposalParametersOptions($proposal->getId());
        list($parametersData, $parametersOptionsIds) = $this->buildParametersData($parametersOptions);
        list($filteredParameterCookieName, $filteredParameterValues) = $this->getFilteredParameterValues($request);

        $category = $proposal->getCategory();

        $additionalCategories = $category->getAdditionalCategories();
        $additionalCategoriesData = array();

        /**
         * @var $additionalCategory Category
         */
        foreach($additionalCategories as $additionalCategory){

            $additionalCategoryProposals = $proposalRepository->findProposals($additionalCategory->getId(), null, $filteredParameterValues, 1, 1);
            if($additionalCategoryProposals){

                $additionalCategoriesData[$additionalCategory->getId()] = array(
                    'category' => $additionalCategory,
                    'proposalData' => current($additionalCategoryProposals),
//                    'manufacturers' => $proposalRepository->findCategoryManufacturers($additionalCategory->getId(), $filteredParameterValues),
                );

            }

        }

        $price = $proposalRepository->findProposalPrice(
            $category->getId(),
            $proposal->getId(),
            array_filter($filteredParameterValues, function($parameterOptionId) use ($parametersOptionsIds) {
                return in_array($parameterOptionId, $parametersOptionsIds);
            })
        );

        $shopCartSummary = $this->getShopCart($request)->getSummary();

        $actions = array();
        if($price instanceof Price){

            $shopCartSummaryPrice = $shopCartSummary['summaryPrice'];
            $shopCartPricesIds = $shopCartSummary['priceIds'];

            if(!in_array($price->getId(), $shopCartPricesIds)){
                $possibleSummaryPrice = ($shopCartSummaryPrice + floatval($price->getValue()));
            } else {
                $possibleSummaryPrice = $shopCartSummaryPrice;
            }

            $shopCartCategoriesIds = $shopCartSummary['categoriesIds'];
            $possibleCategoriesIds = array_unique(array_merge($shopCartCategoriesIds, array($category->getId())));

            if($possibleSummaryPrice && $possibleCategoriesIds){

                /**
                 * @var $actionRepository \Shop\CatalogBundle\Entity\ActionRepository
                 */
                $actionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Action');
                $actions = $actionRepository->findActions($possibleCategoriesIds, $possibleSummaryPrice);

            }

        }

        $response = $this->render('ShopMainBundle:Default:proposal.html.twig', array(
            'settings' => $this->getSettings(),
            'category' => $category,
            'categories' => $this->getCategories(),
            'additionalCategoriesData' => $additionalCategoriesData,
            'proposal' => $proposal,
            'price' => $price,
            'actions' => $actions,
            'shopCartSummary' => $shopCartSummary,
            'parametersData' => $parametersData,
            'filteredParameterValues' => $filteredParameterValues,
        ));

        $response->headers->setCookie(new Cookie($filteredParameterCookieName, json_encode($filteredParameterValues)));

        return $response;

    }

    /**
     * @return object|Settings
     */
    protected function getSettings()
    {
        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if (!$settings) {
            $settings = new Settings();
        }
        return $settings;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getFilteredParameterValues(Request $request)
    {
        $filteredParameterCookieName = 'filteredParameters';
        $filteredParameterCookie = $request->cookies->get($filteredParameterCookieName);
        if ($filteredParameterCookie) {

            $filteredParameterCookie = json_decode($request->cookies->get($filteredParameterCookieName), true);
            if (!is_array($filteredParameterCookie)) {
                $filteredParameterCookie = null;
            }

        }

        $filteredParameterValues = $request->get('parameters', $filteredParameterCookie);
        if (is_array($filteredParameterValues)) {
            $filteredParameterValues = array_filter($filteredParameterValues);
            return array($filteredParameterCookieName, $filteredParameterValues);
        } else {
            $filteredParameterValues = array();
            return array($filteredParameterCookieName, $filteredParameterValues);
        }
    }

    /**
     * @param array $parametersOptions
     * @return array
     */
    protected function buildParametersData(array $parametersOptions)
    {

        $parametersData = array();
        $parametersOptionsIds = array();

        /**
         * @var $parameterOption ParameterOption
         */
        foreach ($parametersOptions as $parameterOption) {

            if (!isset($parametersData[$parameterOption->getParameterId()])) {

                $parametersData[$parameterOption->getParameterId()] = array(
                    'parameter' => $parameterOption->getParameter(),
                    'options' => array(),
                );

            }

            $parametersOptionsIds[] = $parameterOption->getId();
            $parametersData[$parameterOption->getParameterId()]['options'][] = $parameterOption;

        }
        return array($parametersData, $parametersOptionsIds);
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
