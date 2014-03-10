<?php

namespace Shop\MainBundle\Controller;

use Shop\CatalogBundle\Cart\ShopCart;
use Shop\CatalogBundle\Entity\Action;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Shop\CatalogBundle\Entity\Parameter;

/**
 * Class DefaultController
 * @package Shop\MainBundle\Controller
 */
class DefaultController extends Controller
{

    const CATALOG_FILTER_COOKIE_NAME = 'filterValues';

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
     * @return array
     */
    protected function getProposals(){

        $proposals = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Proposal')->findBy(array(
            'status' => Category::STATUS_ON,
            'showOnHomePage' => true
        ));

        return $proposals;

    }

    /**
     * @return array
     */
    protected function getCategories(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findBy(
            array(
                'status' => Category::STATUS_ON
            ),
            array(
                'name' => 'ASC',
            )
        );
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

        $filterParametersValues = $this->getFilterParametersValues($request);

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');

        $manufacturerId = $request->get('manufacturer', $request->cookies->get('manufacturer'));
        $manufacturers = $proposalRepository->findCategoryManufacturers($category->getId());
        $manufacturerIds = array_map(function(Manufacturer $manufacturer){
            return $manufacturer->getId();
        }, $manufacturers);
        if(!in_array($manufacturerId, $manufacturerIds)){
            $manufacturerId = null;
        }

        $parametersOptions = $proposalRepository->findCategoryParametersOptions($category->getId());
        $parametersData = $this->buildParametersData($parametersOptions);

        $filterParametersValuesFilteredByOptionsIds = $this->filterParametersValuesByOptionsIds($filterParametersValues, $parametersData);

        $priceIntervalsData = $proposalRepository->getPriceIntervalsData($category->getId(), $manufacturerId, $filterParametersValuesFilteredByOptionsIds);
        $priceInterval = $priceIntervalsData['interval'];
        $validFilterPrices = array_keys($priceIntervalsData['intervals']);

        $filterPrices = $request->get('prices', json_decode($request->cookies->get('prices' . $category->getId()), true));

        if(is_array($filterPrices)){
            $filterPrices = array_filter($filterPrices, function($filterPrice) use ($validFilterPrices){
                return in_array($filterPrice, $validFilterPrices);
            });
        } else {
            $filterPrices = array();
        }

        $filterPricesRanges = array();
        foreach($filterPrices as $i => $filterPrice){
            $filterPricesRanges[$i] = array(
                'min' => $filterPrice,
                'max' => $filterPrice + $priceInterval,
            );
        }

        $parametersOptionsAmounts = array();
        foreach($category->getParameters() as $categoryParameter){

            if($categoryParameter instanceof CategoryParameter){

                $filterParameterValues = $filterParametersValuesFilteredByOptionsIds;

                if(isset($filterParameterValues[$categoryParameter->getParameterId()])){
                    unset($filterParameterValues[$categoryParameter->getParameterId()]);
                }

                $parametersOptionsAmounts[$categoryParameter->getParameterId()] = $proposalRepository->getParameterOptionsAmounts($categoryParameter->getParameter(), $category->getId(), $manufacturerId, $filterParameterValues, $filterPricesRanges);

            }

        }

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
            $manufacturerId,
            $filterParametersValuesFilteredByOptionsIds,
            $filterPricesRanges
        );

        $viewParameters = array(
            'shopCartSummary' => $this->getShopCart($request)->getSummary(),
            'category' => $category,
            'categories' => $this->getCategories(),
            'proposals' => $proposals,
            'settings' => $this->getSettings(),
            'manufacturers' => $manufacturers,
            'priceIntervalsData' => $priceIntervalsData,
            'filteredPrices' => $filterPrices,
            'parametersData' => $parametersData,
            'extraParametersData' => $extraParametersData,
            'parametersOptionsAmounts' => $parametersOptionsAmounts,
            'filteredManufacturer' => $manufacturerId,
            'filteredParameterValues' => $filterParametersValuesFilteredByOptionsIds,
        );

        $response = $this->render('ShopMainBundle:Default:proposals.html.twig', $viewParameters);
        $response->headers->setCookie(new Cookie(self::CATALOG_FILTER_COOKIE_NAME, json_encode($filterParametersValues)));

        if($request->query->has('manufacturer')){
            $response->headers->setCookie(new Cookie('manufacturer', $manufacturerId));
        }

        if($request->query->has('prices')){
            $response->headers->setCookie(new Cookie('prices' . $category->getId(), json_encode($filterPrices)));
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

        $proposalRepository = $this->getProposalRepository();

        $proposal = $proposalRepository->findOneBy($criteria);

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        $parametersOptions = $proposalRepository->findProposalParametersOptions($proposal->getId());
        $parametersData = $this->buildParametersData($parametersOptions);
        $filterParametersValues = $this->getFilterParametersValues($request);

        $filterParametersValuesFilteredByOptionsIds = $this->filterParametersValuesByOptionsIds($filterParametersValues, $parametersData);

        $category = $proposal->getCategory();

        $priceIntervalsData = $proposalRepository->getPriceIntervalsData($category->getId(), $proposal->getManufacturerId(), $filterParametersValuesFilteredByOptionsIds, $proposal->getId());
        $priceInterval = $priceIntervalsData['interval'];
        $validFilterPrices = array_keys($priceIntervalsData['intervals']);

        $filterPrices = $request->get('prices', json_decode($request->cookies->get('prices' . $category->getId()), true));

        if(is_array($filterPrices)){
            $filterPrices = array_filter($filterPrices, function($filterPrice) use ($validFilterPrices){
                return in_array($filterPrice, $validFilterPrices);
            });
        } else {
            $filterPrices = array();
        }

        $filterPricesRanges = array();
        foreach($filterPrices as $i => $filterPrice){
            $filterPricesRanges[$i] = array(
                'min' => $filterPrice,
                'max' => $filterPrice + $priceInterval,
            );
        }

        $proposalFeatures = array();
        $parametersOptionsAmounts = array();

        foreach($category->getParameters() as $categoryParameter){

            if($categoryParameter instanceof CategoryParameter){

                $proposalFeatures[$categoryParameter->getParameterId()] = null;

                if($categoryParameter->getParameter()->getIsPriceParameter()){

                    $filterParameterValues = $filterParametersValuesFilteredByOptionsIds;

                    if(isset($filterParameterValues[$categoryParameter->getParameterId()])){
                        unset($filterParameterValues[$categoryParameter->getParameterId()]);
                    }

                    $parametersOptionsAmounts[$categoryParameter->getParameterId()] = $proposalRepository->getParameterOptionsAmounts($categoryParameter->getParameter(), $category->getId(), $proposal->getManufacturerId(), $filterParameterValues, $filterPricesRanges, $proposal->getId());

                }

            }

        }

        foreach($proposal->getParameterValues() as $parameterValue){
            if($parameterValue instanceof ParameterValue){
                if(array_key_exists($parameterValue->getParameterId(), $proposalFeatures)){
                    $proposalFeatures[$parameterValue->getParameterId()] = $parameterValue;
                }
            }
        }

        $priceData = $proposalRepository->findProposalPrice(
            $category->getId(),
            $proposal->getId(),
            $filterParametersValuesFilteredByOptionsIds,
            $filterPricesRanges
        );
        $price = $priceData ? $priceData['priceEntity'] : null;

        if($price instanceof Price){

            foreach($price->getParameterValues() as $parameterValue){
                if($parameterValue instanceof ParameterValue){
                    if(array_key_exists($parameterValue->getParameterId(), $proposalFeatures)){
                        $proposalFeatures[$parameterValue->getParameterId()] = $parameterValue;
                    }
                }
            }

        }

        $shopCartSummary = $this->getShopCart($request)->getSummary();

        $actions = array();

        if($price instanceof Price){

            $shopCartSummaryPrice = $shopCartSummary['summaryPrice'];
            $shopCartPricesIds = $shopCartSummary['priceIds'];

            if(!in_array($price->getId(), $shopCartPricesIds)){
                $possibleSummaryPrice = ($shopCartSummaryPrice + floatval($price->getExchangedValue()));
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

        $additionalCategoriesData = $this->getProposalAdditionalCategories($category, $filterParametersValues);

        $response = $this->render('ShopMainBundle:Default:proposal.html.twig', array(
            'settings' => $this->getSettings(),
            'category' => $category,
            'categories' => $this->getCategories(),
            'additionalCategoriesData' => $additionalCategoriesData,
            'proposal' => $proposal,
            'price' => $price,
            'priceData' => $priceData,
            'proposalFeatures' => array_filter($proposalFeatures),
            'actions' => $actions,
            'shopCartSummary' => $shopCartSummary,
            'parametersData' => $parametersData,
            'parametersOptionsAmounts' => $parametersOptionsAmounts,
            'filteredParameterValues' => $filterParametersValuesFilteredByOptionsIds,
            'priceIntervalsData' => $priceIntervalsData,
            'filteredPrices' => $filterPrices,
        ));

        $response->headers->setCookie(new Cookie(self::CATALOG_FILTER_COOKIE_NAME, json_encode($filterParametersValues)));

        if($request->query->has('prices')){
            $response->headers->setCookie(new Cookie('prices' . $category->getId(), json_encode($filterPrices)));
        }

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
    protected function getFilterParametersValues(Request $request)
    {
        $cookieName = self::CATALOG_FILTER_COOKIE_NAME;
        $cookie = $request->cookies->get($cookieName);

        if ($cookie) {

            $cookie = json_decode($request->cookies->get($cookieName), true);
            if (!is_array($cookie)) {
                $cookie = null;
            }

        }

        $values = $request->get('parameters', $cookie);

        if (is_array($values)) {
            $values = array_filter($values);
        } else {
            $values = array();
        }

        return $values;

    }

    /**
     * @param array $parametersOptions
     * @return array
     */
    protected function buildParametersData(array $parametersOptions)
    {

        $parametersData = array();

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

            $parametersData[$parameterOption->getParameterId()]['options'][] = $parameterOption;

        }
        return $parametersData;
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

    /**
     * @param $filteredParametersValues
     * @param $parametersData
     * @return array
     */
    protected function filterParametersValuesByOptionsIds($filteredParametersValues, $parametersData)
    {

        $newFilteredParametersValues = array();

        foreach($parametersData as $parameterId => $parameterData){

            /**
             * @var $parameter Parameter
             */
            $parameter = $parameterData['parameter'];

            $parameterOptionsIds = array_map(function(ParameterOption $parameterOption){
                return $parameterOption->getId();
            }, $parameterData['options']);

            if(isset($filteredParametersValues[$parameterId])){

                $filteredParameterValue = $filteredParametersValues[$parameterId];

                if (is_array($filteredParameterValue)) {

                    $filteredParameterOptionsIds = array();

                    foreach ($filteredParameterValue as $parameterOptionId) {
                        if ($parameterOptionId && in_array($parameterOptionId, $parameterOptionsIds)) {
                            $filteredParameterOptionsIds[] = $parameterOptionId;
                        }
                    }

                    if ($filteredParameterOptionsIds) {
                        $newFilteredParametersValues[$parameterId] = $filteredParameterOptionsIds;
                    }

                } else {

                    $parameterOptionId = $filteredParameterValue;

                    if ($filteredParameterValue && in_array($parameterOptionId, $parameterOptionsIds)) {

                        $newFilteredParametersValues[$parameterId] = $parameterOptionId;

                    }

                }


            } elseif($parameter->getDefaultOptionId() && in_array($parameter->getDefaultOptionId(), $parameterOptionsIds)) {

                $newFilteredParametersValues[$parameterId] = $parameter->getDefaultOptionId();

            }

        }

        return $newFilteredParametersValues;

    }

    /**
     * @param $category
     * @param $filterParametersValues
     * @return array
     */
    protected function getProposalAdditionalCategories(Category $category, $filterParametersValues)
    {

        $proposalRepository = $this->getProposalRepository();

        $additionalCategories = $category->getAdditionalCategories();
        $additionalCategoriesData = array();

        /**
         * @var $additionalCategory Category
         */
        foreach ($additionalCategories as $additionalCategory) {

            $parametersOptions = $proposalRepository->findCategoryParametersOptions($additionalCategory->getId());
            $parametersData = $this->buildParametersData($parametersOptions);

            $filterParametersValuesFilteredByOptionsIds = $this->filterParametersValuesByOptionsIds($filterParametersValues, $parametersData);

            $additionalCategoryProposals = $proposalRepository->findProposals($additionalCategory->getId(), null, $filterParametersValuesFilteredByOptionsIds, null, 1, 1);
            if ($additionalCategoryProposals) {

                $additionalCategoriesData[$additionalCategory->getId()] = array(
                    'category' => $additionalCategory,
                    'proposalData' => current($additionalCategoryProposals),
//                    'manufacturers' => $proposalRepository->findCategoryManufacturers($additionalCategory->getId(), $filteredParameterValues),
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
