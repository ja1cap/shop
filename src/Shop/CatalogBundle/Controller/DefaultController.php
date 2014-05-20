<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Cart\ShopCart;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\ParameterOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO create manufacturer proposals page
 * @TODO create proposals paginator
 * Class DefaultController
 * @package Shop\CatalogBundle\Controller
 */
class DefaultController extends Controller
{

    const CATALOG_FILTER_COOKIE_NAME = 'filterValues';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoriesAction(){

        return $this->render('ShopCatalogBundle:Default:categories.html.twig');

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

                if($categoryParameter->getFilterGroup() == $categoryParameter::FILTER_GROUP_EXTRA && isset($parametersData[$categoryParameter->getParameterId()])){

                    $parameterData = $parametersData[$categoryParameter->getParameterId()];
                    unset($parametersData[$categoryParameter->getParameterId()]);
                    return $parameterData;

                }

                return false;

            })->toArray()
        );

        $proposals = $proposalRepository->findProposalsByParameters(
            $category->getId(),
            $manufacturerId,
            $filterParametersValuesFilteredByOptionsIds,
            $filterPricesRanges
        );

        $shopCartSummary = $this->getShopCartSummary($request);

        $viewParameters = array(
            'shopCartSummary' => $shopCartSummary,
            'category' => $category,
            'categories' => $this->getCategories(),
            'proposals' => $proposals,
            'manufacturers' => $manufacturers,
            'priceIntervalsData' => $priceIntervalsData,
            'filteredPrices' => $filterPrices,
            'parametersData' => $parametersData,
            'extraParametersData' => $extraParametersData,
            'parametersOptionsAmounts' => $parametersOptionsAmounts,
            'filteredManufacturer' => $manufacturerId,
            'filteredParameterValues' => $filterParametersValuesFilteredByOptionsIds,
        );

        $response = $this->render('ShopCatalogBundle:Default:category.html.twig', $viewParameters);
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
         * @var $parameterOption \Shop\CatalogBundle\Entity\ParameterOption
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
     * @param $filteredParametersValues
     * @param $parametersData
     * @return array
     */
    protected function filterParametersValuesByOptionsIds($filteredParametersValues, $parametersData)
    {

        $newFilteredParametersValues = array();

        foreach($parametersData as $parameterId => $parameterData){

            /**
             * @var $parameter \Shop\CatalogBundle\Entity\Parameter
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
     * @return ShopCart
     */
    protected function getShopCart()
    {

        $proposalsRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $categoryRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
        $priceRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price');
        $currencyConverter = $this->get('shop_catalog.price.currency.converter');

        $shopCart = new ShopCart($currencyConverter, $categoryRepository, $proposalsRepository, $priceRepository);

        return $shopCart;

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
